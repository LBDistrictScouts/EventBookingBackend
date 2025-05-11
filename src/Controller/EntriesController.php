<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Entry;
use Cake\Event\EventInterface;
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
     * Index method
     *
     * @return Response|null Renders view
     */
    public function index(): ?Response
    {
        $query = $this->Entries->find()
            ->contain(['Events'])
            ->orderByDesc('reference_number');
        $entries = $this->paginate($query);

        $this->set(compact('entries'));
    }

    /**
     * View method
     *
     * @param string|null $entryId
     * @return Response|null Renders view
     */
    public function view(?string $entryId = null): ?Response
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
     * @return Response|null Redirects on successful add, renders view otherwise.
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
    }

    /**
     * Edit method
     *
     * @param string|null $entryId
     * @return Response|null Redirects on successful edit, renders view otherwise.
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
    }


    /**
     * Lookup method
     *
     * @return Response|null Redirects on successful edit, renders view otherwise.
     */
    public function lookup()
    {
        $this->request->allowMethod(['post']);

        /** @var Entry $entry */
        $entry = $this->Entries->find()
            ->where([
                'reference_number' => $this->request->getData('reference_number'),
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
    }

    /**
     * Delete method
     *
     * @param string|null $entryId Entry id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $entryId = null): ?Response
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
