<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CheckIns Controller
 *
 * @property \App\Model\Table\CheckInsTable $CheckIns
 */
class CheckInsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->CheckIns->find()
            ->contain(['Checkpoints', 'Entries']);
        $checkIns = $this->paginate($query);

        $this->set(compact('checkIns'));
    }

    /**
     * View method
     *
     * @param string|null $id Check In id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $checkIn = $this->CheckIns->get($id, contain: ['Checkpoints', 'Entries', 'Participants']);
        $this->set(compact('checkIn'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $checkIn = $this->CheckIns->newEmptyEntity();
        if ($this->request->is('post')) {
            $checkIn = $this->CheckIns->patchEntity($checkIn, $this->request->getData());
            if ($this->CheckIns->save($checkIn)) {
                $this->Flash->success(__('The check in has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The check in could not be saved. Please, try again.'));
        }
        $checkpoints = $this->CheckIns->Checkpoints->find('list', limit: 200)->all();
        $entries = $this->CheckIns->Entries->find('list', limit: 200)->all();
        $participants = $this->CheckIns->Participants->find('list', limit: 200)->all();
        $this->set(compact('checkIn', 'checkpoints', 'entries', 'participants'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Check In id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $checkIn = $this->CheckIns->get($id, contain: ['Participants']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $checkIn = $this->CheckIns->patchEntity($checkIn, $this->request->getData());
            if ($this->CheckIns->save($checkIn)) {
                $this->Flash->success(__('The check in has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The check in could not be saved. Please, try again.'));
        }
        $checkpoints = $this->CheckIns->Checkpoints->find('list', limit: 200)->all();
        $entries = $this->CheckIns->Entries->find('list', limit: 200)->all();
        $participants = $this->CheckIns->Participants->find('list', limit: 200)->all();
        $this->set(compact('checkIn', 'checkpoints', 'entries', 'participants'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Check In id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $checkIn = $this->CheckIns->get($id);
        if ($this->CheckIns->delete($checkIn)) {
            $this->Flash->success(__('The check in has been deleted.'));
        } else {
            $this->Flash->error(__('The check in could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
