<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Event as EventEntity;
use App\Model\Entity\Section;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\View\JsonView;

/**
 * Sections Controller
 *
 * @property \App\Model\Table\SectionsTable $Sections
 */
class SectionsController extends AppController
{
    /**
     * @return array<class-string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * @param \Cake\Event\EventInterface<static> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // 🔹 Bypass authentication for these actions
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Sections->find()
            ->contain(['ParticipantTypes', 'Groups'])
            ->orderBy(['Groups.sort_order' => 'Asc', 'ParticipantTypes.sort_order' => 'Asc']);

        $sections = $this->paginate($query, ['limit' => 100]);

        $this->set(compact('sections'));
        $this->viewBuilder()->setOption('serialize', ['sections']);
    }

    /**
     * View method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        /** @var \App\Model\Entity\Section $section */
        $section = $this->Sections->get(
            $id,
            contain: [
                'ParticipantTypes',
                'Groups',
                'Events' => [
                    'Checkpoints' => function (SelectQuery $query): SelectQuery {
                        return $query->orderBy(['Checkpoints.checkpoint_sequence' => 'ASC']);
                    },
                ],
                'Participants' => function (SelectQuery $query): SelectQuery {
                    return $query
                        ->contain([
                            'ParticipantTypes',
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
                        ])
                        ->leftJoinWith('ParticipantTypes')
                        ->orderBy([
                            'ParticipantTypes.sort_order' => 'ASC',
                            'Participants.last_name' => 'ASC',
                            'Participants.first_name' => 'ASC',
                        ]);
                },
            ],
        );

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

        $teams = $this->buildSectionTeams($section, $currentEvent?->id);
        $checkpointProgress = $this->buildCheckpointProgressForTeams($teams, $currentEvent, $section, $showAll);

        $this->set(compact('section', 'teams', 'showAll', 'currentEvent', 'checkpointProgress'));
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
     * @param list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}> $teams
     * @param \App\Model\Entity\Event|null $currentEvent
     * @param \App\Model\Entity\Section $section
     * @param bool $showAll
     * @return array{
     *     bars: list<array{sequence: int, sequence_label: string, label: string, count: int, width: float}>,
     *     participant_count: int,
     *     tracked_participant_count: int,
     *     max_count: int
     * }
     */
    private function buildCheckpointProgressForTeams(
        array $teams,
        ?EventEntity $currentEvent,
        Section $section,
        bool $showAll,
    ): array {
        $participants = [];
        $events = [];

        if ($currentEvent instanceof EventEntity && is_string($currentEvent->id) && $currentEvent->id !== '') {
            $events[$currentEvent->id] = $currentEvent;
        }

        if ($showAll || $currentEvent === null) {
            foreach ((array)$section->events as $event) {
                if (!$event instanceof EventEntity || !is_string($event->id) || $event->id === '') {
                    continue;
                }

                $events[$event->id] = $event;
            }
        }

        foreach ($teams as $team) {
            $entryEvent = $team['entry']->event ?? null;
            if ($entryEvent instanceof EventEntity && is_string($entryEvent->id) && $entryEvent->id !== '') {
                $events[$entryEvent->id] = $entryEvent;
            }

            foreach ($team['section_participants'] as $participant) {
                $participants[] = $participant;
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
        $section = $this->Sections->newEmptyEntity();
        if ($this->request->is('post')) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }
        $participantTypes = $this->Sections->ParticipantTypes->find('list', limit: 200)->all();
        $groups = $this->Sections->Groups->find('list', limit: 200)->all();
        $events = $this->Sections->Events->find('list', limit: 200)->all();
        $this->set(compact('section', 'participantTypes', 'groups', 'events'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $section = $this->Sections->get($id, contain: ['Events']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }
        $participantTypes = $this->Sections->ParticipantTypes->find('list', limit: 200)->all();
        $groups = $this->Sections->Groups->find('list', limit: 200)->all();
        $events = $this->Sections->Events->find('list', limit: 200)->all();
        $this->set(compact('section', 'participantTypes', 'groups', 'events'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $section = $this->Sections->get($id);
        if ($this->Sections->delete($section)) {
            $this->Flash->success(__('The section has been deleted.'));
        } else {
            $this->Flash->error(__('The section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
