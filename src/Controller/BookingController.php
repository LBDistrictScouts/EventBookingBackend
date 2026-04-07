<?php
declare(strict_types=1);

namespace App\Controller;

use App\Event\BookingListener;
use App\Model\Entity\Entry;
use App\Model\Table\EntriesTable;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Mailer\MailerAwareTrait;
use Cake\View\JsonView;
use RuntimeException;

/**
 * Entries Controller
 *
 * @property \App\Model\Table\EntriesTable $Entries
 */
class BookingController extends AppController
{
    use MailerAwareTrait;

    private EntriesTable $Entries;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        /** @var \App\Model\Table\EntriesTable $entries */
        $entries = $this->getTableLocator()->get('Entries');
        $this->Entries = $entries;
    }

    /**
     * @return array<class-string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * @param \Cake\Event\EventInterface<static> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // 🔹 Bypass authentication for these actions
        $this->Authentication->allowUnauthenticated(['add', 'edit']);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index(): void
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
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null): void
    {
        $entry = $this->Entries->get($id, contain: ['Events', 'CheckIns', 'Participants']);
        $this->set(compact('entry'));
    }

    /**
     * @param array<string, mixed> $requestData
     * @return array<string, mixed>
     */
    private function prefilterData(array $requestData): array
    {
        // Filter out empty keys in incoming arrays.
        $data = array_filter($requestData, fn($_, $key) => $key !== '', ARRAY_FILTER_USE_BOTH);

        // Ensure 'participants' exists and is an array before processing
        if (!empty($data['participants']) && is_array($data['participants'])) {
            foreach ($data['participants'] as &$participant) {
                // Filter out empty keys in each participant
                $participant = array_filter($participant, fn($_, $key) => $key !== '', ARRAY_FILTER_USE_BOTH);
                $participant = $this->normalizeParticipantData($participant);
            }
            unset($participant); // Unset reference to prevent unexpected behavior
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $participant
     * @return array<string, mixed>
     */
    private function normalizeParticipantData(array $participant): array
    {
        $participantTypeId = $participant['participant_type_id'] ?? $participant['participant_type'] ?? null;
        if (is_string($participantTypeId)) {
            $resolvedParticipantTypeId = $this->resolveParticipantTypeId($participantTypeId);
            if ($resolvedParticipantTypeId !== null) {
                $participant['participant_type_id'] = $resolvedParticipantTypeId;
            }
        }

        $sectionId = $participant['section_id'] ?? $participant['section'] ?? null;
        if (is_string($sectionId)) {
            $resolvedSectionId = $this->resolveSectionId($sectionId);
            if ($resolvedSectionId !== null) {
                $participant['section_id'] = $resolvedSectionId;
            }
        }

        return $participant;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function resolveParticipantTypeId(string $value): ?string
    {
        $trimmedValue = trim($value);
        if ($trimmedValue === '') {
            return null;
        }

        if (preg_match('/^[0-9a-f-]{36}$/i', $trimmedValue) === 1) {
            return $trimmedValue;
        }

        $participantTypes = $this->fetchTable('ParticipantTypes')->find()
            ->all();

        $aliases = [
            'parentsnonuniformedvolunteers' => 'parentnonuniformedvolunteer',
            'leadersvolunteers' => 'leadervolunteer',
        ];
        $normalizedNeedleKey = $this->normalizeLookupValue($trimmedValue);
        $normalizedNeedle = $aliases[$normalizedNeedleKey] ?? $normalizedNeedleKey;

        foreach ($participantTypes as $participantType) {
            if (!$participantType instanceof EntityInterface) {
                continue;
            }

            $candidateKeys = [
                $participantType->get('participant_type'),
                $participantType->get('osm_type_code'),
            ];
            foreach ($candidateKeys as $candidateKey) {
                if (!is_string($candidateKey) || $candidateKey === '') {
                    continue;
                }

                $normalizedCandidateKey = $this->normalizeLookupValue($candidateKey);
                $normalizedCandidate = $aliases[$normalizedCandidateKey] ?? $normalizedCandidateKey;
                if ($normalizedCandidate === $normalizedNeedle) {
                    $resolvedId = $participantType->get('id');

                    return is_string($resolvedId) ? $resolvedId : null;
                }
            }
        }

        return null;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function resolveSectionId(string $value): ?string
    {
        $trimmedValue = trim($value);
        if ($trimmedValue === '') {
            return null;
        }

        if (preg_match('/^[0-9a-f-]{36}$/i', $trimmedValue) === 1) {
            return $trimmedValue;
        }

        $section = $this->fetchTable('Sections')->find()
            ->where(['section_name' => $trimmedValue])
            ->first();

        if ($section === null) {
            return null;
        }

        $resolvedId = $section->get('id');

        return is_string($resolvedId) ? $resolvedId : null;
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeLookupValue(string $value): string
    {
        return strtolower((string)preg_replace('/[^a-z0-9]+/i', '', $value));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add(): ?Response
    {
        $this->request->allowMethod(['post', 'put', 'options']);

        if ($this->request->is(['options'])) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return null;
        }

        $data = $this->prefilterData($this->request->getData());

        $entry = $this->Entries->newEntity($data);
        $entry->set('security_code', '');
        if ($this->Entries->save($entry)) {
            $success = true;
            $message = 'Saved';
            $errors = [];

            $entryId = $entry->get('id');
            if (!is_string($entryId)) {
                throw new RuntimeException('Saved entry is missing an id.');
            }

            $entry = $this->Entries->getApiEntryById($entryId, false);

            $this->getMailer('Booking')->send('confirmation', [$entry]);
            $this->dispatchEvent(BookingListener::EVENT_CREATED, ['entry' => $entry]);
        } else {
            $this->response = $this->response->withStatus(400, 'Validation failed');

            $success = false;
            $message = 'Error';
            $errors = $entry->getErrors();
        }
        $this->set(compact('success', 'entry', 'message', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['success', 'entry', 'message', 'errors']);

        return null;
    }

    /**
     * Edit method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null): ?Response
    {
        $this->request->allowMethod(['patch', 'post', 'put', 'options']);

        if ($this->request->is(['options'])) {
            $message = 'OPTIONS YES';
            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return null;
        }

        $entry = $this->Entries->get($id, contain: ['Participants']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->prefilterData($this->request->getData());
            $participantIdsToKeep = $this->extractParticipantIds($data['participants'] ?? []);
            $participantsToDelete = [];

            if (array_key_exists('participants', $data)) {
                foreach ($entry->get('participants') as $participant) {
                    $participantId = $participant->get('id');
                    if (is_string($participantId) && !isset($participantIdsToKeep[$participantId])) {
                        $participantsToDelete[] = $participant;
                    }
                }
            }

            $entry = $this->Entries->patchEntity($entry, $data);
            if (!$entry instanceof Entry) {
                throw new RuntimeException('Patched entry has unexpected type.');
            }

            $errors = [];
            $saved = $this->Entries->getConnection()->transactional(
                function () use ($entry, $participantsToDelete): bool {
                    if (!$this->Entries->save($entry)) {
                        return false;
                    }

                    foreach ($participantsToDelete as $participant) {
                        if (!$this->Entries->Participants->delete($participant)) {
                            return false;
                        }
                    }

                    return true;
                },
            );

            if ($saved) {
                $success = true;
                $message = 'Saved';
                $errors = [];
                $mailEntry = $this->Entries->getApiEntryById((string)$id, false);
                $this->getMailer('Booking')->send('confirmation', [$mailEntry, 'updated']);
                $entry = $mailEntry->hidePublicFields();
            } else {
                $this->response = $this->response->withStatus(400, 'Validation failed');
                $success = false;
                $message = 'Error';
                $errors = $entry->getErrors();
                $entry = $entry->hidePublicFields();
            }
            $this->set(compact('success', 'entry', 'message', 'errors'));
            $this->viewBuilder()->setClassName(JsonView::class);
            $this->viewBuilder()->setOption('serialize', ['success', 'entry', 'message', 'errors']);

            return null;
        }

        $events = $this->fetchTable('Events')->find('list', limit: 200)->all();
        $this->set(compact('entry', 'events'));

        return null;
    }

    /**
     * @param array<int, mixed> $participants
     * @return array<string, bool>
     */
    private function extractParticipantIds(array $participants): array
    {
        $participantIds = [];

        foreach ($participants as $participant) {
            if (!is_array($participant)) {
                continue;
            }

            $participantId = $participant['id'] ?? null;
            if (is_string($participantId) && $participantId !== '') {
                $participantIds[$participantId] = true;
            }
        }

        return $participantIds;
    }
}
