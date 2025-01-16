<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\View\JsonView;

/**
 * Checkpoints Controller
 *
 * @property \App\Model\Table\CheckpointsTable $Checkpoints
 */
class CheckpointsController extends AppController
{
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Checkpoints->find()
            ->contain(['Events']);
        $checkpoints = $this->paginate($query);

        $this->set(compact('checkpoints'));
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
        $checkpoint = $this->Checkpoints->get($checkpointId, contain: ['Events', 'CheckIns']);
        $this->set(compact('checkpoint'));
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
