<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\View\JsonView;

/**
 * CheckIns Controller
 *
 * @property \App\Model\Table\CheckInsTable $CheckIns
 */
class CheckInsController extends AppController
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
        $this->Authentication->allowUnauthenticated(['add']);
    }

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
    public function view(?string $id = null)
    {
        $checkIn = $this->CheckIns->get($id, contain: [
            'Checkpoints',
            'Entries',
            'Participants' => [
                'Sections',
                'ParticipantTypes',
            ],
        ]);
        $this->set(compact('checkIn'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add(?string $entryId = null)
    {
        if ($this->request->is(['options'])) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return;
        }

        $checkIn = $this->CheckIns->newEmptyEntity();
        if ($entryId) {
            $checkIn->set('entry_id', $entryId);
        }

        if ($this->request->is('post')) {
            /** @var array $data */
            $data = $this->request->getData();

            if (array_key_exists('participants', $data)) {
                $participants = $data['participants'];

                if (!array_key_exists('_ids', $participants)) {
                    $data['participants'] = ['_ids' => $participants];
                }
            }

            $checkIn = $this->CheckIns->patchEntity(
                entity: $checkIn,
                data: $data,
                options: ['associated' => 'Participants'],
            );

            if (!$checkIn->hasValue('check_in_time')) {
                $checkIn->set('check_in_time', date('Y-m-d H:i:s'));
            }

            if ($this->CheckIns->save($checkIn)) {
                $this->Flash->success(__('The check in has been saved.'));

                if (str_contains($this->request->getPath(), '.json')) {
                    $this->set(compact('checkIn'));
                    $this->viewBuilder()->setOption('serialize', ['checkIn']);

                    return;
                } else {
                    return $this->redirect([
                        'controller' => 'Entries',
                        'action' => 'view',
                        $checkIn->entry_id,
                    ]);
                }
            }
            $this->Flash->error(__('The check in could not be saved. Please, try again.'));

            if (str_contains($this->request->getPath(), '.json')) {
                $this->response->withStatus(400);

                $this->set(compact('checkIn'));
                $this->viewBuilder()->setOption('serialize', ['checkIn']);

                return;
            }
        }
        $checkIn->set('check_in_time', date('Y-m-d H:i:s'));

        $checkpoints = $this->CheckIns->Checkpoints->find()
            ->orderByAsc('checkpoint_sequence')
            ->limit(100)
            ->all();
        $checkpoints = collection($checkpoints)
            ->combine('id', function ($checkpoint) {
                return '[' . $checkpoint->checkpoint_sequence . '] ' . $checkpoint->checkpoint_name;
            })
            ->toArray();

        $entryFixed = false;

        if ($entryId) {
            $entries = $this->CheckIns->Entries->find('list', conditions: ['id' => $entryId], limit: 200)->all();
            $entryFixed = true;
            $participants = $this->CheckIns->Participants->find(
                'list',
                valueField: 'full_name',
                keyField: 'id',
                conditions: [
                    'entry_id' => $entryId,
                    'checked_out' => false,
                ],
                limit: 200,
            )->all();
        } else {
            $entries = $this->CheckIns->Entries->find('list', limit: 200)->all();
            $participants = $this->CheckIns->Participants->find(
                'list',
                valueField: 'full_name',
                keyField: 'id',
                groupField: 'entry.entry_name',
                conditions: [
                    'checked_out' => false,
                ],
                contain: 'Entries',
            )->all();
        }

        $this->set(
            compact('checkIn', 'checkpoints', 'entries', 'participants', 'entryFixed', 'entryId'),
        );
    }

    /**
     * Edit method
     *
     * @param string|null $id Check In id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
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
    public function delete(?string $id = null)
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
