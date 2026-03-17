<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\CheckIn;
use App\Model\Entity\Entry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Mailer\MailerAwareTrait;
use Cake\View\JsonView;
use RuntimeException;

/**
 * Entries Controller
 *
 * @property \App\Model\Table\EntriesTable $Entries
 */
class EntriesController extends AppController
{
    use MailerAwareTrait;

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
        $this->Authentication->allowUnauthenticated(['lookup', 'view']);
    }

    /**
     * @var array<string, mixed> Pagination defaults.
     */
    protected array $paginate = [
        'limit' => 25,
        'order' => [
            'event_id' => 'asc',
            'reference_number' => 'desc',
        ],
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index(): void
    {
        $query = $this->Entries->find()
            ->contain(['Events']);
        $entries = $this->paginate($query);

        $this->set(compact('entries'));
    }

    /**
     * Search for an entry by its visible booking reference and redirect to the entry view.
     *
     * Accepts either BOOKINGCODE-123 or just 123. Numeric-only references are resolved
     * against the current active event first, then globally if unambiguous.
     *
     * @return \Cake\Http\Response
     */
    public function findByReference(): Response
    {
        $fallback = ['controller' => 'Events', 'action' => 'current'];
        $rawReference = strtoupper(trim((string)$this->request->getQuery('reference')));
        if ($rawReference === '') {
            $this->Flash->error(__('Enter an entry reference.'));

            return $this->redirect($fallback) ?? $this->response;
        }

        $query = $this->Entries->find()
            ->contain(['Events']);

        if (preg_match('/^([A-Z0-9]+)\s*-\s*(\d+)$/', $rawReference, $matches) === 1) {
            $query->matching('Events', function ($q) use ($matches) {
                return $q->where(['Events.booking_code' => $matches[1]]);
            })->where(['Entries.reference_number' => (int)$matches[2]]);

            $entry = $query->first();
            if ($entry !== null) {
                return $this->redirectResponse(['action' => 'view', $entry->id]);
            }

            $this->Flash->error(__('No entry was found for reference {0}.', $rawReference));

            return $this->redirect($fallback) ?? $this->response;
        }

        if (!ctype_digit($rawReference)) {
            $this->Flash->error(__('Entry references must look like BOOKINGCODE-123 or just 123.'));

            return $this->redirect($fallback) ?? $this->response;
        }

        $referenceNumber = (int)$rawReference;
        $activeEvent = $this->Entries->Events->find()
            ->select(['id'])
            ->where(['bookable' => true, 'finished' => false])
            ->orderByAsc('start_time')
            ->first();

        if ($activeEvent !== null) {
            $activeEntry = $this->Entries->find()
                ->where([
                    'Entries.event_id' => $activeEvent->id,
                    'Entries.reference_number' => $referenceNumber,
                ])
                ->first();

            if ($activeEntry !== null) {
                return $this->redirectResponse(['action' => 'view', $activeEntry->id]);
            }
        }

        $matches = $this->Entries->find()
            ->where(['Entries.reference_number' => $referenceNumber])
            ->limit(2)
            ->all();

        if ($matches->count() === 1) {
            /** @var \App\Model\Entity\Entry|null $entry */
            $entry = $matches->first();
            if ($entry === null) {
                $this->Flash->error(__('No entry was found for reference {0}.', $rawReference));

                return $this->redirect($fallback) ?? $this->response;
            }

            return $this->redirectResponse(['action' => 'view', $entry->id]);
        }

        $message = $matches->count() > 1
            ? __('Reference {0} matches multiple events. Use the full reference code.', $rawReference)
            : __('No entry was found for reference {0}.', $rawReference);
        $this->Flash->error($message);

        return $this->redirect($fallback) ?? $this->response;
    }

    /**
     * View method
     *
     * @param string|null $entryId
     * @return void
     */
    public function view(?string $entryId = null): void
    {
        if ($this->request->getParam('_ext') === 'json') {
            $this->request->allowMethod(['get', 'options']);

            if ($this->request->is('options')) {
                $message = 'OPTIONS YES';
                $this->set(compact('message'));
                $this->viewBuilder()->setOption('serialize', ['message']);

                return;
            }

            $entry = $this->Entries->getApiEntryById((string)$entryId);
            $this->setPublicEntryResponse($entry);

            return;
        }

        /** @var \App\Model\Entity\Entry $entry */
        $entry = $this->Entries->get($entryId, contain: [
            'Events',
            'CheckIns' => [
                'Checkpoints',
                'Entries',
                'Participants' => [
                    'ParticipantTypes',
                    'Sections',
                ],
            ],
            'Participants' => [
                'Sections',
                'ParticipantTypes',
            ],
        ]);

        /** @var list<\App\Model\Entity\CheckIn> $checkIns */
        $checkIns = $entry->check_ins;
        usort($checkIns, function (CheckIn $left, CheckIn $right): int {
            return $left->check_in_time <=> $right->check_in_time;
        });
        $entry->set('check_ins', $checkIns);

        $this->set(compact('entry'));
    }

    /**
     * Merge one entry into another entry in the same event.
     *
     * GET renders the interface, POST renders confirmation or performs the merge.
     *
     * @param string|null $consumedId Entry that will be consumed.
     * @param string|null $survivorId Optional surviving entry id.
     * @return \Cake\Http\Response|null
     */
    public function merge(?string $consumedId = null, ?string $survivorId = null): ?Response
    {
        $this->request->allowMethod(['get', 'post']);

        $baseEntryId = $consumedId ?: $survivorId;
        /** @var \App\Model\Entity\Entry $baseEntry */
        $baseEntry = $this->Entries->get((string)$baseEntryId, contain: ['Events']);

        /** @var \Cake\Collection\CollectionInterface<int, \App\Model\Entity\Entry> $allMergeEntries */
        $allMergeEntries = $this->Entries->find()
            ->contain(['Events', 'Participants'])
            ->where([
                'Entries.event_id' => $baseEntry->event_id,
            ])
            ->orderByAsc('Entries.reference_number')
            ->all();

        $selectedConsumedId = $consumedId;
        $selectedSurvivorId = $survivorId;
        if ($this->request->is('post')) {
            $selectedConsumedId = (string)$this->request->getData('consumed_entry_id');
            $selectedSurvivorId = (string)$this->request->getData('persisting_entry_id');
        }

        $consumedEntry = null;
        if ($selectedConsumedId !== null && $selectedConsumedId !== '') {
            /** @var \App\Model\Entity\Entry|null $consumedEntry */
            $consumedEntry = $this->Entries->find()
                ->contain(['Events', 'Participants'])
                ->where([
                    'Entries.id' => $selectedConsumedId,
                    'Entries.event_id' => $baseEntry->event_id,
                ])
                ->first();
        }

        $survivorEntry = null;
        if ($selectedSurvivorId !== null && $selectedSurvivorId !== '') {
            /** @var \App\Model\Entity\Entry|null $survivorEntry */
            $survivorEntry = $this->Entries->find()
                ->contain(['Events', 'Participants'])
                ->where([
                    'Entries.id' => $selectedSurvivorId,
                    'Entries.event_id' => $baseEntry->event_id,
                ])
                ->first();
        }

        if ($consumedEntry === null) {
            /** @var \App\Model\Entity\Entry $consumedEntry */
            $consumedEntry = $this->Entries->get((string)$baseEntryId, contain: ['Events', 'Participants']);
        }

        if (
            $this->request->is('post') &&
            ($survivorEntry === null || $consumedEntry === null || $survivorEntry->id === $consumedEntry->id)
        ) {
            $this->Flash->error(__('Choose two different valid entries from the same event.'));

            return $this->redirect(['action' => 'merge', $consumedEntry->id, $selectedSurvivorId]);
        }

        $isConfirmation = false;
        if ($this->request->is('post') && $survivorEntry !== null) {
            $isConfirmation = true;
            if ((bool)$this->request->getData('confirmed')) {
                $result = $this->Entries->mergeEntries($survivorEntry->id, $consumedEntry->id);
                if ($result === false) {
                    $this->Flash->error(__('The entry could not be merged. Please try again.'));

                    return $this->redirect(['action' => 'merge', $consumedEntry->id, $survivorEntry->id]);
                }

                $notificationEntry = $this->Entries->getApiEntryById($survivorEntry->id, false);
                $this->getMailer('Booking')->send('confirmation', [$notificationEntry, 'merged', $consumedEntry]);

                $this->Flash->success(__(
                    'Merged {0} into {1}. The surviving entry now has {2} participant(s).',
                    $consumedEntry->entry_name,
                    $survivorEntry->entry_name,
                    $result,
                ));

                return $this->redirect(['action' => 'view', $survivorEntry->id]);
            }
        }

        $this->set(compact('consumedEntry', 'survivorEntry', 'allMergeEntries', 'isConfirmation'));

        return null;
    }

    /**
     * Return merge preview data for the selected survivor.
     *
     * @param string|null $consumedId
     * @param string|null $survivorId
     * @return void
     */
    public function mergePreview(?string $consumedId = null, ?string $survivorId = null): void
    {
        $this->request->allowMethod(['get']);

        /** @var \App\Model\Entity\Entry $consumedEntry */
        $consumedEntry = $this->Entries->get((string)$consumedId, contain: ['Events', 'Participants']);
        /** @var \App\Model\Entity\Entry|null $survivorEntry */
        $survivorEntry = $this->Entries->find()
            ->contain(['Events', 'Participants'])
            ->where([
                'Entries.id' => $survivorId,
                'Entries.event_id' => $consumedEntry->event_id,
                'Entries.id !=' => $consumedEntry->id,
            ])
            ->first();

        if ($survivorEntry === null) {
            throw new NotFoundException('Merge survivor not found');
        }

        $preview = [
            'consumed' => $this->buildMergeEntrySummary($consumedEntry),
            'survivor' => $this->buildMergeEntrySummary($survivorEntry),
            'merged_participant_count' => count($consumedEntry->participants) + count($survivorEntry->participants),
        ];

        $this->set(compact('preview'));
        $this->viewBuilder()->setOption('serialize', ['preview']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add(): ?Response
    {
        $entry = $this->Entries->newEmptyEntity();
        $participantRows = [$this->emptyParticipantRow()];
        $participantErrors = [];

        if ($this->request->is('post')) {
            /** @var array<string, mixed> $requestData */
            $requestData = $this->request->getData();
            $participantRows = $this->normalizeParticipantRows($requestData['participants'] ?? []);
            if ($participantRows === []) {
                $participantRows = [$this->emptyParticipantRow()];
                unset($requestData['participants']);
            } else {
                $requestData['participants'] = array_map(
                    fn(array $participantRow): array => [
                        'first_name' => $participantRow['first_name'],
                        'last_name' => $participantRow['last_name'],
                        'participant_type_id' => $participantRow['participant_type_id'],
                        'section_id' => $participantRow['section_id'] !== '' ? $participantRow['section_id'] : null,
                        'checked_in' => false,
                        'checked_out' => false,
                        'highest_check_in_sequence' => 0,
                    ],
                    $participantRows,
                );
            }

            $entry = $this->Entries->patchEntity($entry, $requestData, [
                'associated' => ['Participants'],
            ]);
            if (!$entry instanceof Entry) {
                throw new RuntimeException('Patched entry has unexpected type.');
            }

            if ($this->Entries->save($entry, ['associated' => ['Participants']])) {
                $mailEntry = $this->Entries->getApiEntryById((string)$entry->id, false);
                $this->getMailer('Booking')->send('confirmation', [$mailEntry]);
                $this->Flash->success(__('The entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            /** @var list<\App\Model\Entity\Participant> $participants */
            $participants = (array)$entry->participants;
            foreach ($participants as $index => $participant) {
                $participantErrors[$index] = $participant->getErrors();
            }
            $this->Flash->error(__('The entry could not be saved. Please, try again.'));
        }
        $events = $this->Entries->Events->find('list', limit: 200)->all();
        $participantTypes = $this->Entries->Participants->ParticipantTypes->find(
            'list',
            keyField: 'id',
            valueField: 'participant_type',
            orderBy: ['ParticipantTypes.sort_order' => 'ASC'],
        )->all();
        $sections = $this->Entries->Participants->Sections->find(
            'list',
            keyField: 'id',
            valueField: 'section_name',
            groupField: 'group.group_name',
            contain: ['Groups', 'ParticipantTypes'],
            orderBy: [
                'Groups.sort_order' => 'ASC',
                'ParticipantTypes.sort_order' => 'ASC',
                'Sections.section_name' => 'ASC',
            ],
        )->all();
        $this->set(compact('entry', 'events', 'participantTypes', 'sections', 'participantRows', 'participantErrors'));

        return null;
    }

    /**
     * Edit method
     *
     * @param string|null $entryId
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit(?string $entryId = null): ?Response
    {
        $entry = $this->Entries->get($entryId, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $entry = $this->Entries->patchEntity($entry, $this->request->getData());
            if ($this->Entries->save($entry)) {
                $this->Flash->success(__('The entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The entry could not be saved. Please, try again.'));
        }
        $events = $this->Entries->Events->find('list', limit: 200)->all();
        $this->set(compact('entry', 'events'));

        return null;
    }

    /**
     * Lookup method
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function lookup(): ?Response
    {
        $this->request->allowMethod(['post', 'options']);

        if ($this->request->is('options')) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return null;
        }

        $reference_number = $this->request->getData('reference_number');

        if (empty($reference_number) || !is_numeric($reference_number)) {
            $message = 'Invalid Lookup Data';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            $this->response = $this->response->withStatus(400);

            return null;
        }

        try {
            $entry = $this->Entries->getApiEntryByLookup(
                (int)$reference_number,
                (string)$this->request->getData('security_code'),
            );
            $this->setPublicEntryResponse($entry);
        } catch (RecordNotFoundException $e) {
            $message = 'Invalid Lookup';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            $this->response = $this->response->withStatus(404);

            return null;
        }

        return null;
    }

    /**
     * @param \App\Model\Entity\Entry $entry
     * @return void
     */
    protected function setPublicEntryResponse(Entry $entry): void
    {
        $this->set(compact('entry'));
        $this->viewBuilder()->setOption('serialize', ['entry']);
    }

    /**
     * @param mixed $participantData
     * @return list<array{first_name: string, last_name: string, participant_type_id: string, section_id: string}>
     */
    protected function normalizeParticipantRows(mixed $participantData): array
    {
        if (!is_array($participantData)) {
            return [];
        }

        $participantRows = [];
        foreach ($participantData as $row) {
            if (!is_array($row)) {
                continue;
            }

            $participantRow = [
                'first_name' => trim((string)($row['first_name'] ?? '')),
                'last_name' => trim((string)($row['last_name'] ?? '')),
                'participant_type_id' => trim((string)($row['participant_type_id'] ?? '')),
                'section_id' => trim((string)($row['section_id'] ?? '')),
            ];

            if (
                $participantRow['first_name'] === '' &&
                $participantRow['last_name'] === '' &&
                $participantRow['participant_type_id'] === '' &&
                $participantRow['section_id'] === ''
            ) {
                continue;
            }

            $participantRows[] = $participantRow;
        }

        return $participantRows;
    }

    /**
     * @return array{first_name: string, last_name: string, participant_type_id: string, section_id: string}
     */
    protected function emptyParticipantRow(): array
    {
        return [
            'first_name' => '',
            'last_name' => '',
            'participant_type_id' => '',
            'section_id' => '',
        ];
    }

    /**
     * @param \App\Model\Entity\Entry $entry
     * @return array<string, mixed>
     */
    protected function buildMergeEntrySummary(Entry $entry): array
    {
        return [
            'id' => $entry->id,
            'entry_name' => $entry->entry_name,
            'reference' => $entry->event->booking_code . '-' . $entry->reference_number,
            'entry_email' => (string)$entry->entry_email,
            'entry_mobile' => (string)$entry->entry_mobile,
            'participant_count' => count($entry->participants),
            'participants' => array_map(
                static fn($participant): array => [
                    'id' => $participant->id,
                    'name' => trim((string)$participant->full_name),
                    'checked_in' => (bool)$participant->checked_in,
                    'checked_out' => (bool)$participant->checked_out,
                    'highest_check_in_sequence' => (int)$participant->highest_check_in_sequence,
                ],
                $entry->participants,
            ),
        ];
    }

    /**
     * @param array<string|int, mixed>|string $url
     * @return \Cake\Http\Response
     */
    protected function redirectResponse(array|string $url): Response
    {
        return $this->redirect($url) ?? $this->response;
    }

    /**
     * Delete method
     *
     * @param string|null $entryId Entry id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $entryId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entry = $this->Entries->get($entryId);
        if ($this->Entries->delete($entry)) {
            $this->Flash->success(__('The entry has been deleted.'));
        } else {
            $this->Flash->error(__('The entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
