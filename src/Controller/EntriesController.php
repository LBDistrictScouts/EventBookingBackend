<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
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
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // 🔹 Bypass authentication for these actions
        $this->Authentication->allowUnauthenticated(['lookup']);
    }

    /**
     * @var array $paginate Pagination Default Array.
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
     * @return \Cake\Http\Response|null Renders view
     */
    public function index()
    {
        $query = $this->Entries->find()
            ->contain(['Events']);
        $entries = $this->paginate($query);

        $this->set(compact('entries'));
    }

    /**
     * View method
     *
     * @param string|null $entryId
     * @return \Cake\Http\Response|null Renders view
     */
    public function view(?string $entryId = null)
    {
        $entry = $this->Entries->get($entryId, contain: [
            'Events',
            'CheckIns' => [
                'sort' => 'Checkpoints.checkpoint_sequence',
                'Checkpoints',
                'Entries',
            ],
            'Participants' => [
                'Sections',
                'ParticipantTypes',
            ],
        ]);
        $this->set(compact('entry'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
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
    }

    /**
     * Edit method
     *
     * @param string|null $entryId
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit(?string $entryId = null)
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
    }

    /**
     * Lookup method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function lookup()
    {
        $this->request->allowMethod(['post', 'options']);

        if ($this->request->is('options')) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return;
        }

        $reference_number = $this->request->getData('reference_number');

        if (empty($reference_number) || !is_numeric($reference_number)) {
            $message = 'Invalid Lookup Data';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            $this->response = $this->response->withStatus(400);

            return;
        }

        try {
            /** @var \App\Model\Entity\Entry $entry */
            $entry = $this->Entries->find()
                ->where([
                    'reference_number' => $reference_number,
                    'security_code' => $this->request->getData('security_code'),
                ])
                ->contain(['Participants'])
                ->firstOrFail();

            $entry = $entry->setHidden(
                fields: [
                    'security_code',
                    'entry_email',
                    'entry_mobile',
                    'active',
                    'deleted',
                ],
                merge: true,
            );

            $this->set(compact('entry'));
            $this->viewBuilder()->setOption('serialize', ['entry']);
        } catch (RecordNotFoundException $e) {
            $message = 'Invalid Lookup';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            $this->response = $this->response->withStatus(404);

            return;
        }
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
