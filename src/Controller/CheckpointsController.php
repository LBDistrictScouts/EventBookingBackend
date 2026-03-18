<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Checkpoint;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\View\JsonView;

/**
 * Checkpoints Controller
 *
 * @property \App\Model\Table\CheckpointsTable $Checkpoints
 */
class CheckpointsController extends AppController
{
    /**
     * @param \App\Model\Entity\Checkpoint $checkpoint
     * @return array{
     *     previousCheckpoint: \App\Model\Entity\Checkpoint|null,
     *     betweenParticipants: \Cake\Datasource\ResultSetInterface<int|string, \App\Model\Entity\Participant>,
     *     beforeParticipantCount: int,
     *     betweenParticipantCount: int,
     *     checkedInHereParticipantCount: int,
     *     stillWalkingParticipantCount: int,
     *     checkedOutParticipantCount: int
     * }
     */
    protected function buildCheckpointDashboardData(Checkpoint $checkpoint): array
    {
        /** @var \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Checkpoint> $previousCheckpointQuery */
        $previousCheckpointQuery = $this->Checkpoints->find()
            ->where([
                'Checkpoints.event_id' => $checkpoint->event_id,
                'Checkpoints.checkpoint_sequence <' => $checkpoint->checkpoint_sequence,
            ]);

        if ($checkpoint->checkpoint_sequence >= 0) {
            $previousCheckpointQuery->where([
                'Checkpoints.checkpoint_sequence >=' => 0,
            ]);
        }

        /** @var \App\Model\Entity\Checkpoint|null $previousCheckpoint */
        $previousCheckpoint = $previousCheckpointQuery
            ->orderByDesc('Checkpoints.checkpoint_sequence')
            ->first();

        /** @var \App\Model\Table\ParticipantsTable $participantsTable */
        $participantsTable = $this->fetchTable('Participants');
        $betweenParticipantsQuery = $participantsTable->find(
            'betweenSequences',
            sequence: $checkpoint->checkpoint_sequence,
            minimumSequence: $previousCheckpoint?->checkpoint_sequence,
            eventId: $checkpoint->event_id,
        )
            ->contain([
                'Entries.Events',
                'ParticipantTypes',
                'Sections',
            ])
            ->orderBy([
                'Participants.highest_check_in_sequence' => 'DESC',
                'Participants.last_name' => 'ASC',
                'Participants.first_name' => 'ASC',
            ]);

        /** @var \Cake\Datasource\ResultSetInterface<int|string, \App\Model\Entity\Participant> $betweenParticipants */
        $betweenParticipants = $betweenParticipantsQuery->all();
        $beforeParticipantCount = $participantsTable->find(
            'activeBeforeSequence',
            sequence: $checkpoint->checkpoint_sequence,
            eventId: $checkpoint->event_id,
        )->count();
        $checkedInHereParticipantCount = $participantsTable->find(
            'reachedSequence',
            sequence: $checkpoint->checkpoint_sequence,
            eventId: $checkpoint->event_id,
        )->count();
        $stillWalkingParticipantCount = $participantsTable->find(
            'stillWalking',
            sequence: $checkpoint->checkpoint_sequence,
            eventId: $checkpoint->event_id,
        )->count();
        $checkedOutParticipantCount = $participantsTable->find(
            'checkedOut',
            eventId: $checkpoint->event_id,
        )->count();

        return [
            'previousCheckpoint' => $previousCheckpoint,
            'betweenParticipants' => $betweenParticipants,
            'beforeParticipantCount' => $beforeParticipantCount,
            'betweenParticipantCount' => $betweenParticipants->count(),
            'checkedInHereParticipantCount' => $checkedInHereParticipantCount,
            'stillWalkingParticipantCount' => $stillWalkingParticipantCount,
            'checkedOutParticipantCount' => $checkedOutParticipantCount,
        ];
    }

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
            'event_id' => 'asc',
            'checkpoint_sequence' => 'asc',
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Checkpoints->find()->contain(['Events']);
        $showAll = $this->request->getQuery('all') === '1';
        $currentEvent = null;

        if (!$showAll) {
            try {
                /** @var \App\Model\Table\EventsTable $eventsTable */
                $eventsTable = $this->fetchTable('Events');
                $currentEvent = $eventsTable->find()
                    ->where(['bookable' => true, 'finished' => false])
                    ->orderByAsc('start_time')
                    ->firstOrFail();

                $query->where(['Checkpoints.event_id' => $currentEvent->id]);
            } catch (RecordNotFoundException) {
                $currentEvent = null;
            }
        }

        $checkpoints = $this->paginate($query);

        $this->set(compact('checkpoints', 'showAll', 'currentEvent'));
        $this->viewBuilder()->setOption('serialize', ['checkpoints']);
    }

    /**
     * View method
     *
     * @param string|null $checkpointId Checkpoint id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $checkpointId = null)
    {
        /** @var \App\Model\Entity\Checkpoint $checkpoint */
        $checkpoint = $this->Checkpoints->get($checkpointId, contain: [
            'Events',
            'CheckIns' => [
                'Entries',
                'Participants',
                'sort' => ['CheckIns.check_in_time' => 'DESC'],
            ],
        ]);
        $dashboardData = $this->buildCheckpointDashboardData($checkpoint);
        $previousCheckpoint = $dashboardData['previousCheckpoint'];
        $betweenParticipants = $dashboardData['betweenParticipants'];
        $beforeParticipantCount = $dashboardData['beforeParticipantCount'];
        $betweenParticipantCount = $dashboardData['betweenParticipantCount'];
        $checkedInHereParticipantCount = $dashboardData['checkedInHereParticipantCount'];
        $stillWalkingParticipantCount = $dashboardData['stillWalkingParticipantCount'];
        $checkedOutParticipantCount = $dashboardData['checkedOutParticipantCount'];
        /** @var \App\Model\Table\CheckInsTable $checkInsTable */
        $checkInsTable = $this->fetchTable('CheckIns');
        $checkIn = $checkInsTable->newEmptyEntity();
        $checkIn->set('checkpoint_id', $checkpoint->id);
        $checkIn->set('check_in_time', date('Y-m-d H:i:s'));
        $selectedEntryId = '';
        $selectedEntryReference = '';
        $selectedEntryLabel = '';
        $checkpointParticipants = [];

        $fragment = (string)$this->request->getQuery('fragment');
        $isAjax = $this->request->is('ajax');
        if ($isAjax && in_array($fragment, ['count', 'table', 'recent'], true)) {
            $this->viewBuilder()->disableAutoLayout();
            $this->set(compact(
                'checkpoint',
                'previousCheckpoint',
                'betweenParticipants',
                'beforeParticipantCount',
                'betweenParticipantCount',
                'checkedInHereParticipantCount',
                'stillWalkingParticipantCount',
                'checkedOutParticipantCount',
            ));

            return $this->render(sprintf('/element/Checkpoints/%s_fragment', $fragment));
        }

        $this->set(compact(
            'checkpoint',
            'previousCheckpoint',
            'betweenParticipants',
            'beforeParticipantCount',
            'betweenParticipantCount',
            'checkedInHereParticipantCount',
            'stillWalkingParticipantCount',
            'checkedOutParticipantCount',
            'checkIn',
            'selectedEntryId',
            'selectedEntryReference',
            'selectedEntryLabel',
            'checkpointParticipants',
        ));
        $this->viewBuilder()->setOption('serialize', ['checkpoint']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $checkpoint = $this->Checkpoints->newEmptyEntity();
        if ($this->request->is('post')) {
            $checkpoint = $this->Checkpoints->patchEntity($checkpoint, $this->request->getData());
            if ($this->Checkpoints->save($checkpoint)) {
                $this->Flash->success(__('The checkpoint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The checkpoint could not be saved. Please, try again.'));
        }
        $events = $this->Checkpoints->Events->find('list', limit: 200)->all();
        $this->set(compact('checkpoint', 'events'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Checkpoint id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $checkpoint = $this->Checkpoints->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $checkpoint = $this->Checkpoints->patchEntity($checkpoint, $this->request->getData());
            if ($this->Checkpoints->save($checkpoint)) {
                $this->Flash->success(__('The checkpoint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The checkpoint could not be saved. Please, try again.'));
        }
        $events = $this->Checkpoints->Events->find('list', limit: 200)->all();
        $this->set(compact('checkpoint', 'events'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Checkpoint id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $checkpoint = $this->Checkpoints->get($id);
        if ($this->Checkpoints->delete($checkpoint)) {
            $this->Flash->success(__('The checkpoint has been deleted.'));
        } else {
            $this->Flash->error(__('The checkpoint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
