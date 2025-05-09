<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\EntriesTable;
use Cake\Event\EventInterface;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Table;
use Cake\View\JsonView;

/**
 * Entries Controller
 *
 * @property \App\Model\Table\EntriesTable $Entries
 */
class BookingController extends AppController
{
    use MailerAwareTrait;

    private EntriesTable|Table $Entries;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Entries = $this->getTableLocator()->get('Entries');
    }

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
        $query = $this->Entries->find()
            ->contain(['Events']);
        $entries = $this->paginate($query);

        $this->set(compact('entries'));
    }

    /**
     * View method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $entry = $this->Entries->get($id, contain: ['Events', 'CheckIns', 'Participants']);
        $this->set(compact('entry'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod(['post', 'put', 'options']);

        if ($this->request->is(['options'])) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return;
        }

        // Filter out empty keys in incoming arrays.
        $data = array_filter($this->request->getData(), fn ($_, $key) => $key !== '', ARRAY_FILTER_USE_BOTH);

        // Ensure 'participants' exists and is an array before processing
        if (!empty($data['participants']) && is_array($data['participants'])) {
            foreach ($data['participants'] as &$participant) {
                // Filter out empty keys in each participant
                $participant = array_filter($participant, fn ($_, $key) => $key !== '', ARRAY_FILTER_USE_BOTH);
            }
            unset($participant); // Unset reference to prevent unexpected behavior
        }

        $entry = $this->Entries->newEntity($data);
        $entry->set('security_code', '');
        if ($this->Entries->save($entry)) {
            $success = true;
            $message = 'Saved';
            $errors = [];

            $entry = $this->Entries->get($entry->id, contain: [
                'Events' => ['Checkpoints', 'Questions'],
                'CheckIns',
                'Participants',
            ]);

            $this->getMailer('Booking')->send('confirmation', [$entry]);
        } else {
            $this->response = $this->response->withStatus(400, 'Validation failed');

            $success = false;
            $message = 'Error';
            $errors = $entry->getErrors();
        }
        $this->set(compact('success', 'entry', 'message', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['success', 'entry', 'message', 'errors']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $entry = $this->Entries->get($id, contain: []);
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
     * Delete method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entry = $this->Entries->get($id);
        if ($this->Entries->delete($entry)) {
            $this->Flash->success(__('The entry has been deleted.'));
        } else {
            $this->Flash->error(__('The entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
