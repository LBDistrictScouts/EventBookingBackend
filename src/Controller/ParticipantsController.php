<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Participants Controller
 *
 * @property \App\Model\Table\ParticipantsTable $Participants
 */
class ParticipantsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Participants->find()
            ->contain(['Entries', 'ParticipantTypes', 'Sections']);
        $participants = $this->paginate($query);

        $enhanced = $this->request->getQuery('enhanced') ?? false;

        $this->set(compact('participants', 'enhanced'));
    }

    /**
     * View method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $participant = $this->Participants->get(
            $id,
            contain: ['Entries', 'ParticipantTypes', 'Sections', 'CheckIns.Checkpoints'],
        );
        $this->set(compact('participant'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $participant = $this->Participants->newEmptyEntity();
        if ($this->request->is('post')) {
            $participant = $this->Participants->patchEntity($participant, $this->request->getData());
            if ($this->Participants->save($participant)) {
                $this->Flash->success(__('The participant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant could not be saved. Please, try again.'));
        }
        $entries = $this->Participants->Entries->find('list', limit: 200)->all();
        $participantTypes = $this->Participants->ParticipantTypes->find('list', limit: 200)->all();
        $sections = $this->Participants->Sections->find('list', limit: 200)->all();
        $checkIns = $this->Participants->CheckIns->find('list', limit: 200)->all();
        $this->set(compact('participant', 'entries', 'participantTypes', 'sections', 'checkIns'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $participant = $this->Participants->get($id, contain: ['CheckIns']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $participant = $this->Participants->patchEntity($participant, $this->request->getData());
            if ($this->Participants->save($participant)) {
                $this->Flash->success(__('The participant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant could not be saved. Please, try again.'));
        }
        $entries = $this->Participants->Entries->find('list', limit: 200)->all();
        $participantTypes = $this->Participants->ParticipantTypes->find('list', limit: 200)->all();
        $sections = $this->Participants->Sections->find('list', limit: 200)->all();
        $checkIns = $this->Participants->CheckIns->find('list', limit: 200)->all();
        $this->set(compact('participant', 'entries', 'participantTypes', 'sections', 'checkIns'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $participant = $this->Participants->get($id);
        if ($this->Participants->delete($participant)) {
            $this->Flash->success(__('The participant has been deleted.'));
        } else {
            $this->Flash->error(__('The participant could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
