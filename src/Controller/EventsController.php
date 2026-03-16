<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\View\JsonView;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 */
class EventsController extends AppController
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
        $this->Authentication->allowUnauthenticated(['current']);
    }

    /**
     * Index method
     *
     * @return void Renders view
     */
    public function index(): void
    {
        $query = $this->Events->find();
        $events = $this->paginate($query);

        $this->set(compact('events'));
        $this->viewBuilder()->setOption('serialize', ['events']);
    }

    /**
     * @return void
     */
    public function current(): void
    {
        try {
            $this->viewBuilder()->setTemplate('view');

            $query = $this->Events->find()->where(['bookable' => true, 'finished' => false])->orderByAsc('start_time');
            $latest = $query->firstOrFail();
            $this->view($latest->id);
        } catch (RecordNotFoundException $exception) {
            if (str_contains($this->request->getPath(), '.json')) {
                throw $exception;
            }

            $setupSections = [
                [
                    'title' => 'Create an event',
                    'description' => 'Start by creating the event that bookings and check-ins will belong to.',
                    'links' => [
                        ['label' => 'Add event', 'url' => ['action' => 'add']],
                        ['label' => 'View events', 'url' => ['action' => 'index']],
                    ],
                ],
                [
                    'title' => 'Define sections and groups',
                    'description' => 'Groups and sections provide the structure used when collecting bookings.',
                    'links' => [
                        ['label' => 'Add group', 'url' => ['controller' => 'Groups', 'action' => 'add']],
                        ['label' => 'Add section', 'url' => ['controller' => 'Sections', 'action' => 'add']],
                        ['label' => 'View groups', 'url' => ['controller' => 'Groups', 'action' => 'index']],
                        ['label' => 'View sections', 'url' => ['controller' => 'Sections', 'action' => 'index']],
                    ],
                ],
                [
                    'title' => 'Add participant types',
                    'description' =>
                        'Participant types drive pricing and category choices for each section.',
                    'links' => [
                        [
                            'label' => 'Add participant type',
                            'url' => ['controller' => 'ParticipantTypes', 'action' => 'add'],
                        ],
                        [
                            'label' => 'View participant types',
                            'url' => ['controller' => 'ParticipantTypes', 'action' => 'index'],
                        ],
                    ],
                ],
                [
                    'title' => 'Prepare checkpoints and questions',
                    'description' => 'Optional checkpoints and booking questions can be added once the event exists.',
                    'links' => [
                        ['label' => 'Add checkpoint', 'url' => ['controller' => 'Checkpoints', 'action' => 'add']],
                        ['label' => 'Add question', 'url' => ['controller' => 'Questions', 'action' => 'add']],
                    ],
                ],
            ];

            $this->viewBuilder()->setTemplate('setup_dashboard');
            $this->set(compact('setupSections'));
        }
    }

    /**
     * View method
     *
     * @param string|null $id Event id.
     * @return void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        if (str_contains($this->request->getPath(), '.json')) {
            $event = $this->Events->get(
                $id,
                fields: [
                    'id',
                    'event_name',
                    'event_description',
                    'booking_code',
                    'start_time',
                    'bookable',
                    'finished',
                ],
                contain: [
                    'Sections' => [
                        'Groups',
                        'ParticipantTypes',
                        'fields' => [
                            'section_name',
                            'Groups.group_name',
                            'Groups.sort_order',
                            'ParticipantTypes.participant_type',
                            'ParticipantTypes.adult',
                            'ParticipantTypes.uniformed',
                            'ParticipantTypes.out_of_district',
                            'ParticipantTypes.category',
                            'ParticipantTypes.sort_order',
                        ],
                    ],
                    'Questions' => [
                        'fields' => ['Questions.id', 'event_id', 'question_text', 'answer_text'],
                    ],
                    'Checkpoints' => [
                        'sort' => 'Checkpoints.checkpoint_sequence',
                        'fields' => ['id', 'checkpoint_sequence', 'checkpoint_name', 'event_id'],
                    ],
                ],
            );
            $event->setHidden(['Checkpoints.event_id', 'Questions.event_id', 'event_id']);
            $this->set(compact('event'));
            $this->viewBuilder()->setOption('serialize', ['event']);

            return;
        }

        $event = $this->Events->get(
            $id,
            contain: [
                'Sections' => [
                    'Groups',
                    'ParticipantTypes',
                    'sort' => ['Groups.sort_order' => 'ASC', 'ParticipantTypes.sort_order' => 'ASC'],
                ],
                'Questions',
                'Checkpoints' => ['sort' => 'checkpoint_sequence'],
                'Entries' => [
                    'Participants',
                    'CheckIns.Checkpoints',
                    'sort' => ['reference_number' => 'ASC'],
                ],
            ],
        );
        $this->set(compact('event'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $event = $this->Events->newEmptyEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $sections = $this->Events->Sections
            ->find(
                'list',
                keyField: 'id',
                valueField: 'section_name',
                groupField: 'group.group_name',
                limit: 200,
            )
            ->contain(['Groups', 'ParticipantTypes'])
            ->orderBy(['Groups.sort_order' => 'ASC', 'ParticipantTypes.sort_order' => 'ASC'])
            ->all();
        $this->set(compact('event', 'sections'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $event = $this->Events->get($id, contain: ['Sections']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $sections = $this->Events->Sections->find('list', limit: 200)->all();
        $this->set(compact('event', 'sections'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
