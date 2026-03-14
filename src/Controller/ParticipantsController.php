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

        $checkedIn = $this->request->getQuery('checked-in') ?? false;
        if ($checkedIn) {
            $query->where(['checked_in' => true]);
        }

        $checkedOut = $this->request->getQuery('checked-out') ?? false;
        if ($checkedOut) {
            $query->where(['checked_out' => true]);
        }

        $participants = $this->paginate($query);

        $this->set(compact(
            'participants',
            'checkedIn',
            'checkedOut',
        ));
    }

    /**
     * View method
     *
     * @param string|null $id Participant id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
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
     * @param string|null $entryId
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add(?string $entryId = null)
    {
        $participant = $this->Participants->newEmptyEntity();
        $participant->set('entry_id', $entryId);

        if ($this->request->is('post')) {
            $participant = $this->Participants->patchEntity($participant, $this->request->getData());
            if ($this->Participants->save($participant)) {
                $this->Flash->success(__('The participant has been saved.'));

                if ($entryId) {
                    return $this->redirect(['controller' => 'Entries', 'action' => 'view', $entryId]);
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant could not be saved. Please, try again.'));
        }
        $entries = $this->Participants->Entries->find('list', limit: 200)->all();
        $participantTypes = $this->Participants->ParticipantTypes->find('list', limit: 200)->all();
        $sections = $this->Participants->Sections->find(
            'list',
            limit: 200,
            contain: ['Groups', 'ParticipantTypes'],
            keyField: 'id',
            valueField: 'section_name',
            groupField: 'group.group_name',
            sort: ['Groups.sort_order' => 'ASC', 'ParticipantTypes.sort_order' => 'ASC'],
        )->all();
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
    public function edit(?string $id = null)
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
        $sections = $this->Participants->Sections->find(
            'list',
            limit: 200,
            contain: ['Groups', 'ParticipantTypes'],
            keyField: 'id',
            valueField: 'section_name',
            groupField: 'group.group_name',
            sort: ['Groups.sort_order' => 'ASC', 'ParticipantTypes.sort_order' => 'ASC'],
        )->all();
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
    public function delete(?string $id = null)
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
