<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Entry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\JsonView;

/**
 * Entries Controller
 *
 * @property \App\Model\Table\EntriesTable $Entries
 */
class EntriesController extends AppController
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
        $rawReference = strtoupper(trim((string)$this->request->getQuery('reference')));
        if ($rawReference === '') {
            $this->Flash->error(__('Enter an entry reference.'));

            return $this->redirect($this->referer(['controller' => 'Events', 'action' => 'current'], true));
        }

        $query = $this->Entries->find()
            ->contain(['Events']);

        if (preg_match('/^([A-Z0-9]+)\s*-\s*(\d+)$/', $rawReference, $matches) === 1) {
            $query->matching('Events', function ($q) use ($matches) {
                return $q->where(['Events.booking_code' => $matches[1]]);
            })->where(['Entries.reference_number' => (int)$matches[2]]);

            $entry = $query->first();
            if ($entry !== null) {
                return $this->redirect(['action' => 'view', $entry->id]);
            }

            $this->Flash->error(__('No entry was found for reference {0}.', $rawReference));

            return $this->redirect($this->referer(['controller' => 'Events', 'action' => 'current'], true));
        }

        if (!ctype_digit($rawReference)) {
            $this->Flash->error(__('Entry references must look like BOOKINGCODE-123 or just 123.'));

            return $this->redirect($this->referer(['controller' => 'Events', 'action' => 'current'], true));
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
                return $this->redirect(['action' => 'view', $activeEntry->id]);
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

                return $this->redirect($this->referer(['controller' => 'Events', 'action' => 'current'], true));
            }

            return $this->redirect(['action' => 'view', $entry->id]);
        }

        $message = $matches->count() > 1
            ? __('Reference {0} matches multiple events. Use the full reference code.', $rawReference)
            : __('No entry was found for reference {0}.', $rawReference);
        $this->Flash->error($message);

        return $this->redirect($this->referer(['controller' => 'Events', 'action' => 'current'], true));
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

        usort($entry->check_ins, function ($left, $right): int {
            $leftSequence = (int)$left->checkpoint->checkpoint_sequence;
            $rightSequence = (int)$right->checkpoint->checkpoint_sequence;

            $leftIsNegative = $leftSequence < 0;
            $rightIsNegative = $rightSequence < 0;

            if ($leftIsNegative !== $rightIsNegative) {
                return $leftIsNegative ? 1 : -1;
            }

            if ($leftIsNegative) {
                return $leftSequence <=> $rightSequence;
            }

            return $leftSequence <=> $rightSequence;
        });

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
        $baseEntry = $this->Entries->get((string)$baseEntryId, contain: ['Events']);

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
            $survivorEntry = $this->Entries->find()
                ->contain(['Events', 'Participants'])
                ->where([
                    'Entries.id' => $selectedSurvivorId,
                    'Entries.event_id' => $baseEntry->event_id,
                ])
                ->first();
        }

        if ($consumedEntry === null) {
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

        $consumedEntry = $this->Entries->get((string)$consumedId, contain: ['Events', 'Participants']);
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
        if ($this->request->is('post')) {
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
     * @param \App\Model\Entity\Entry $entry
     * @return array<string, scalar>
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
