<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Event as EventEntity;
use App\Model\Entity\Group;
use App\Model\Entity\Section;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query\SelectQuery;
use Cake\View\JsonView;

/**
 * Groups Controller
 *
 * @property \App\Model\Table\GroupsTable $Groups
 */
class GroupsController extends AppController
{
    /**
     * @return array<class-string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * @var array<string, mixed> Pagination defaults.
     */
    protected array $paginate = [
        'limit' => 25,
        'order' => [
            'sort_order' => 'asc',
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Groups->find();
        $groups = $this->paginate($query);

        $this->set(compact('groups'));
        $this->viewBuilder()->setOption('serialize', ['groups']);
    }

    /**
     * View method
     *
     * @param string|null $id Group id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        /** @var \App\Model\Entity\Group $group */
        $group = $this->Groups->get($id, contain: [
            'Sections' => function (SelectQuery $query): SelectQuery {
                return $query
                    ->contain([
                        'ParticipantTypes',
                        'Participants' => function (SelectQuery $query): SelectQuery {
                            return $query
                                ->contain([
                                    'Entries' => [
                                        'Events' => [
                                            'Checkpoints' => function (SelectQuery $query): SelectQuery {
                                                return $query->orderBy(['Checkpoints.checkpoint_sequence' => 'ASC']);
                                            },
                                        ],
                                        'Participants' => function (SelectQuery $query): SelectQuery {
                                            return $query
                                                ->contain(['ParticipantTypes', 'Sections'])
                                                ->leftJoinWith('ParticipantTypes')
                                                ->orderBy([
                                                    'ParticipantTypes.sort_order' => 'ASC',
                                                    'Participants.last_name' => 'ASC',
                                                    'Participants.first_name' => 'ASC',
                                                ]);
                                        },
                                    ],
                                    'ParticipantTypes',
                                ])
                                ->leftJoinWith('ParticipantTypes')
                                ->orderBy([
                                    'ParticipantTypes.sort_order' => 'ASC',
                                    'Participants.last_name' => 'ASC',
                                    'Participants.first_name' => 'ASC',
                                ]);
                        },
                    ])
                    ->leftJoinWith('ParticipantTypes')
                    ->orderBy([
                        'ParticipantTypes.sort_order' => 'ASC',
                        'Sections.section_name' => 'ASC',
                    ]);
            },
        ]);

        $showAll = $this->request->getQuery('all') === '1';
        $currentEvent = null;
        if (!$showAll) {
            try {
                /** @var \App\Model\Table\EventsTable $eventsTable */
                $eventsTable = $this->fetchTable('Events');
                $currentEvent = $eventsTable->find()
                    ->contain([
                        'Checkpoints' => function (SelectQuery $query): SelectQuery {
                            return $query->orderBy(['Checkpoints.checkpoint_sequence' => 'ASC']);
                        },
                    ])
                    ->where(['bookable' => true, 'finished' => false])
                    ->orderByAsc('start_time')
                    ->firstOrFail();
            } catch (RecordNotFoundException) {
                $currentEvent = null;
            }
        }

        $eventId = $this->request->getQuery('event_id');

        if ($eventId) {
            /** @var \App\Model\Table\SectionsTable $sectionsTable */
            $sectionsTable = $this->fetchTable('Sections');
            $billing = $sectionsTable->find()
                ->contain(['ParticipantTypes'])
                ->matching('Participants', function (SelectQuery $query) use ($eventId): SelectQuery {
                    return $query
                        ->matching('Entries', function (SelectQuery $query) use ($eventId): SelectQuery {
                            return $query->where(['Entries.event_id' => $eventId]);
                        })
                        ->matching('ParticipantTypes', function (SelectQuery $query): SelectQuery {
                            return $query->where([
                                'ParticipantTypes.uniformed' => true,
                                'ParticipantTypes.adult' => false,
                            ]);
                        })
                        ->where(['Participants.checked_in' => true]);
                })
                ->where(['Sections.group_id' => $id])
                ->select([
                    'Sections.id',
                    'Sections.section_name',
                    'uniformed_members' => $sectionsTable->find()->func()->count('Participants.id'),
                ])
                ->groupBy([
                    'Sections.id',
                    'Sections.section_name',
                ]);

            $this->set('billing', $billing);
        }

        $sectionSummaries = $this->buildSectionSummaries($group, $currentEvent?->id);
        $checkpointProgress = $this->buildCheckpointProgressForGroup($sectionSummaries, $currentEvent);

        $this->set(compact('group', 'sectionSummaries', 'showAll', 'currentEvent', 'checkpointProgress'));
    }

    /**
     * @param \App\Model\Entity\Group $group
     * @param string|null $eventId
     * @return list<array{section: \App\Model\Entity\Section, teams: list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}>, team_count: int, section_participant_count: int}>
     */
    private function buildSectionSummaries(Group $group, ?string $eventId = null): array
    {
        $summaries = [];

        foreach ((array)$group->sections as $section) {
            if (!$section instanceof Section) {
                continue;
            }

            $teams = $this->buildSectionTeams($section, $eventId);
            $summaries[] = [
                'section' => $section,
                'teams' => $teams,
                'team_count' => count($teams),
                'section_participant_count' => count((array)$section->participants),
            ];
        }

        return $summaries;
    }

    /**
     * @param \App\Model\Entity\Section $section
     * @param string|null $eventId
     * @return list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}>
     */
    private function buildSectionTeams(Section $section, ?string $eventId = null): array
    {
        $teamsByEntry = [];

        foreach ((array)$section->participants as $participant) {
            $entry = $participant->entry ?? null;
            if ($entry === null || !is_string($entry->id) || $entry->id === '') {
                continue;
            }
            if ($eventId !== null && $entry->event_id !== $eventId) {
                continue;
            }

            $entryId = $entry->id;
            if (!isset($teamsByEntry[$entryId])) {
                $teamsByEntry[$entryId] = [
                    'entry' => $entry,
                    'section_participants' => [],
                    'participant_count' => count((array)$entry->participants),
                    'section_participant_count' => 0,
                ];
            }

            $teamsByEntry[$entryId]['section_participants'][] = $participant;
            $teamsByEntry[$entryId]['section_participant_count']++;
        }

        $teams = array_values($teamsByEntry);
        usort($teams, static function (array $left, array $right): int {
            $leftStart = $left['entry']->event->start_time->getTimestamp();
            $rightStart = $right['entry']->event->start_time->getTimestamp();
            if ($leftStart !== $rightStart) {
                return $leftStart <=> $rightStart;
            }

            $leftReference = (int)($left['entry']->reference_number ?? PHP_INT_MAX);
            $rightReference = (int)($right['entry']->reference_number ?? PHP_INT_MAX);
            if ($leftReference !== $rightReference) {
                return $leftReference <=> $rightReference;
            }

            return strcasecmp((string)$left['entry']->entry_name, (string)$right['entry']->entry_name);
        });

        return $teams;
    }

    /**
     * @param list<array{section: \App\Model\Entity\Section, teams: list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}>, team_count: int, section_participant_count: int}> $sectionSummaries
     * @param \App\Model\Entity\Event|null $currentEvent
     * @return array{
     *     bars: list<array{sequence: int, sequence_label: string, label: string, count: int, width: float}>,
     *     participant_count: int,
     *     tracked_participant_count: int,
     *     max_count: int
     * }
     */
    private function buildCheckpointProgressForGroup(array $sectionSummaries, ?EventEntity $currentEvent): array
    {
        $participants = [];
        $events = [];

        if ($currentEvent instanceof EventEntity && is_string($currentEvent->id) && $currentEvent->id !== '') {
            $events[$currentEvent->id] = $currentEvent;
        }

        foreach ($sectionSummaries as $summary) {
            foreach ($summary['teams'] as $team) {
                $entryEvent = $team['entry']->event ?? null;
                if ($entryEvent instanceof EventEntity && is_string($entryEvent->id) && $entryEvent->id !== '') {
                    $events[$entryEvent->id] = $entryEvent;
                }

                foreach ($team['section_participants'] as $participant) {
                    $participants[] = $participant;
                }
            }
        }

        return $this->buildCheckpointProgressData($participants, array_values($events));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $group = $this->Groups->newEmptyEntity();
        if ($this->request->is('post')) {
            $group = $this->Groups->patchEntity($group, $this->request->getData());
            if ($this->Groups->save($group)) {
                $this->Flash->success(__('The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The group could not be saved. Please, try again.'));
        }
        $this->set(compact('group'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Group id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $group = $this->Groups->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $group = $this->Groups->patchEntity($group, $this->request->getData());
            if ($this->Groups->save($group)) {
                $this->Flash->success(__('The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The group could not be saved. Please, try again.'));
        }
        $this->set(compact('group'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Group id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $group = $this->Groups->get($id);
        if ($this->Groups->delete($group)) {
            $this->Flash->success(__('The group has been deleted.'));
        } else {
            $this->Flash->error(__('The group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
