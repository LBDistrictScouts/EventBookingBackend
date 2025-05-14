<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\View\JsonView;

/**
 * Sections Controller
 *
 * @property \App\Model\Table\SectionsTable $Sections
 */
class SectionsController extends AppController
{
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
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Sections->find()
            ->contain(['ParticipantTypes', 'Groups'])
            ->orderBy(['Groups.sort_order' => 'Asc', 'ParticipantTypes.sort_order' => 'Asc']);

        $sections = $this->paginate($query, ['limit' => 100]);

        $this->set(compact('sections'));
        $this->viewBuilder()->setOption('serialize', ['sections']);
    }

    /**
     * View method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $section = $this->Sections->get(
            $id,
            contain: [
                'ParticipantTypes',
                'Groups',
                'Events',
                'Participants' => ['ParticipantTypes', 'Entries']
            ],
        );
        $this->set(compact('section'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $section = $this->Sections->newEmptyEntity();
        if ($this->request->is('post')) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }
        $participantTypes = $this->Sections->ParticipantTypes->find('list', limit: 200)->all();
        $groups = $this->Sections->Groups->find('list', limit: 200)->all();
        $events = $this->Sections->Events->find('list', limit: 200)->all();
        $this->set(compact('section', 'participantTypes', 'groups', 'events'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $section = $this->Sections->get($id, contain: ['Events']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }
        $participantTypes = $this->Sections->ParticipantTypes->find('list', limit: 200)->all();
        $groups = $this->Sections->Groups->find('list', limit: 200)->all();
        $events = $this->Sections->Events->find('list', limit: 200)->all();
        $this->set(compact('section', 'participantTypes', 'groups', 'events'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Section id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $section = $this->Sections->get($id);
        if ($this->Sections->delete($section)) {
            $this->Flash->success(__('The section has been deleted.'));
        } else {
            $this->Flash->error(__('The section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
