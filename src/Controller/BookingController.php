<?php
declare(strict_types=1);

namespace App\Controller;

use App\Event\BookingListener;
use App\Model\Entity\Entry;
use App\Model\Entity\Participant;
use App\Model\Table\EntriesTable;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Query\SelectQuery;
use Cake\View\JsonView;
use DateTimeInterface;
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

        $entry = $this->Entries->get($id, contain: [
            'Participants' => fn(SelectQuery $query): SelectQuery => $query->find('withTrashed'),
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->prefilterData($this->request->getData());
            $patchedParticipants = null;
            if (isset($data['participants']) && is_array($data['participants'])) {
                $data['participants'] = $this->matchSubmittedParticipantsToExisting(
                    $data['participants'],
                    (array)$entry->get('participants'),
                );
                $patchedParticipants = $this->buildPatchedParticipants(
                    $data['participants'],
                    (array)$entry->get('participants'),
                );
            }
            $participantIdsToKeep = $this->extractParticipantIds($data['participants'] ?? []);
            $participantsToDelete = [];

            if (array_key_exists('participants', $data)) {
                foreach ($entry->get('participants') as $participant) {
                    if ($participant->get('deleted') !== null) {
                        continue;
                    }

                    $participantId = $participant->get('id');
                    if (is_string($participantId) && !isset($participantIdsToKeep[$participantId])) {
                        $participantsToDelete[] = $participant;
                    }
                }
            }

            unset($data['participants']);
            $entry = $this->Entries->patchEntity($entry, $data);
            if (!$entry instanceof Entry) {
                throw new RuntimeException('Patched entry has unexpected type.');
            }
            if (is_array($patchedParticipants)) {
                $entry->set('participants', $patchedParticipants);
                $entry->setDirty('participants', true);
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

    /**
     * @param array<int, mixed> $submittedParticipants
     * @param array<int, \App\Model\Entity\Participant> $existingParticipants
     * @return array<int, mixed>
     */
    private function matchSubmittedParticipantsToExisting(
        array $submittedParticipants,
        array $existingParticipants,
    ): array {
        $participantsByAccessKey = [];
        foreach ($existingParticipants as $participant) {
            if (!$participant instanceof Participant) {
                continue;
            }

            $accessKey = $participant->get('access_key');
            if (!is_string($accessKey) || $accessKey === '') {
                continue;
            }

            $currentMatch = $participantsByAccessKey[$accessKey] ?? null;
            if (
                !$currentMatch instanceof Participant ||
                $this->shouldPreferParticipantMatch($participant, $currentMatch)
            ) {
                $participantsByAccessKey[$accessKey] = $participant;
            }
        }

        foreach ($submittedParticipants as &$participant) {
            if (!is_array($participant)) {
                continue;
            }

            $participantId = $participant['id'] ?? null;
            if (is_string($participantId) && $participantId !== '') {
                continue;
            }

            $accessKey = $participant['access_key'] ?? null;
            if (!is_string($accessKey) || $accessKey === '') {
                continue;
            }

            $matchedParticipant = $participantsByAccessKey[$accessKey] ?? null;
            if (!$matchedParticipant instanceof Participant) {
                continue;
            }

            $matchedParticipantId = $matchedParticipant->get('id');
            if (!is_string($matchedParticipantId) || $matchedParticipantId === '') {
                continue;
            }

            $participant['id'] = $matchedParticipantId;
            if ($matchedParticipant->get('deleted') !== null) {
                $participant['deleted'] = null;
            }
        }
        unset($participant);

        return $submittedParticipants;
    }

    /**
     * @param array<int, mixed> $submittedParticipants
     * @param array<int, \App\Model\Entity\Participant> $existingParticipants
     * @return array<int, \App\Model\Entity\Participant>
     */
    private function buildPatchedParticipants(array $submittedParticipants, array $existingParticipants): array
    {
        $existingParticipantsById = [];
        foreach ($existingParticipants as $participant) {
            if (!$participant instanceof Participant) {
                continue;
            }

            $participantId = $participant->get('id');
            if (is_string($participantId) && $participantId !== '') {
                $existingParticipantsById[$participantId] = $participant;
            }
        }

        $patchedParticipants = [];
        foreach ($submittedParticipants as $participantData) {
            if (!is_array($participantData)) {
                continue;
            }

            $participantId = $participantData['id'] ?? null;
            if (
                is_string($participantId) &&
                $participantId !== '' &&
                isset($existingParticipantsById[$participantId])
            ) {
                $patchedParticipant = $this->Entries->Participants->patchEntity(
                    $existingParticipantsById[$participantId],
                    $participantData,
                    ['accessibleFields' => ['id' => true]],
                );
                if (!$patchedParticipant instanceof Participant) {
                    throw new RuntimeException('Patched participant has unexpected type.');
                }

                $patchedParticipants[] = $patchedParticipant;

                continue;
            }

            $newParticipant = $this->Entries->Participants->newEntity($participantData);
            if (!$newParticipant instanceof Participant) {
                throw new RuntimeException('New participant has unexpected type.');
            }

            $patchedParticipants[] = $newParticipant;
        }

        return $patchedParticipants;
    }

    /**
     * @param \App\Model\Entity\Participant $candidate
     * @param \App\Model\Entity\Participant $current
     * @return bool
     */
    private function shouldPreferParticipantMatch(Participant $candidate, Participant $current): bool
    {
        $candidateDeleted = $candidate->get('deleted') !== null;
        $currentDeleted = $current->get('deleted') !== null;
        if ($candidateDeleted !== $currentDeleted) {
            return !$candidateDeleted;
        }

        return $this->participantTimestamp($candidate) >= $this->participantTimestamp($current);
    }

    /**
     * @param \App\Model\Entity\Participant $participant
     * @return int
     */
    private function participantTimestamp(Participant $participant): int
    {
        $modified = $participant->get('modified');
        if ($modified instanceof DateTimeInterface) {
            return $modified->getTimestamp();
        }

        $created = $participant->get('created');
        if ($created instanceof DateTimeInterface) {
            return $created->getTimestamp();
        }

        return 0;
    }
}
