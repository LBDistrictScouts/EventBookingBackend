<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\View\JsonView;

/**
 * ParticipantTypes Controller
 *
 * @property \App\Model\Table\ParticipantTypesTable $ParticipantTypes
 */
class ParticipantTypesController extends AppController
{
    /**
     * @return array<class-string>
     */
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
        $query = $this->ParticipantTypes->find();
        $participantTypes = $this->paginate($query);

        $this->set(compact('participantTypes'));
        $this->viewBuilder()->setOption('serialize', ['participantTypes']);
    }

    /**
     * View method
     *
     * @param string|null $id Participant Type id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $participantType = $this->ParticipantTypes->get($id, contain: ['Participants', 'Sections']);
        $this->set(compact('participantType'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $participantType = $this->ParticipantTypes->newEmptyEntity();
        if ($this->request->is('post')) {
            $participantType = $this->ParticipantTypes->patchEntity($participantType, $this->request->getData());
            if ($this->ParticipantTypes->save($participantType)) {
                $this->Flash->success(__('The participant type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant type could not be saved. Please, try again.'));
        }
        $this->set(compact('participantType'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Participant Type id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $participantType = $this->ParticipantTypes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $participantType = $this->ParticipantTypes->patchEntity($participantType, $this->request->getData());
            if ($this->ParticipantTypes->save($participantType)) {
                $this->Flash->success(__('The participant type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participant type could not be saved. Please, try again.'));
        }
        $this->set(compact('participantType'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Participant Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $participantType = $this->ParticipantTypes->get($id);
        if ($this->ParticipantTypes->delete($participantType)) {
            $this->Flash->success(__('The participant type has been deleted.'));
        } else {
            $this->Flash->error(__('The participant type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
