<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Model\Entity\Entry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\EntriesController Test Case
 *
 * @uses \App\Controller\EntriesController
 */
class EntriesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

    private const FIXTURE_ENTRY_ID = '2342ad37-13f0-4fd1-bd3f-2032273626ce';

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Groups',
        'app.ParticipantTypes',
        'app.Sections',

        'app.Events',
        'app.EventsSections',
        'app.Checkpoints',

        'app.Entries',
        'app.Participants',

        'app.CheckIns',
        'app.ParticipantsCheckIns',

        'app.Questions',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EntriesController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/entries');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EntriesController::view()
     */
    public function testView(): void
    {
        $this->get('/entries/view/' . self::FIXTURE_ENTRY_ID);
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Merge Entry');
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::merge()
     */
    public function testMergeGetRendersSelectionInterface(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Merge Interface Event',
            'event_description' => 'Event for merge UI',
            'booking_code' => 'MRGUI',
            'start_time' => '2027-01-01 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);
        $this->assertNotFalse($events->save($event));

        $survivor = $this->createSearchableEntry('MRGUI', 10, false, $event->id);
        $victim = $this->createSearchableEntry('MRGUI', 11, false, $event->id);

        $this->get('/entries/merge/' . $victim->id . '/' . $survivor->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Consumed Entry');
        $this->assertResponseContains('Surviving Entry');
        $this->assertResponseContains('Review Merge');
        $this->assertResponseContains('data-preview-base');
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::mergePreview()
     */
    public function testMergePreviewReturnsSelectedMergeInformation(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Merge Preview Event',
            'event_description' => 'Event for merge preview',
            'booking_code' => 'MRGPV',
            'start_time' => '2027-01-01 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);
        $this->assertNotFalse($events->save($event));

        $survivor = $this->createSearchableEntry('MRGPV', 21, false, $event->id);
        $victim = $this->createSearchableEntry('MRGPV', 22, false, $event->id);

        $this->get('/entries/merge-preview/' . $victim->id . '/' . $survivor->id . '.json');

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('MRGPV-21', $data['preview']['survivor']['reference']);
        $this->assertSame('MRGPV-22', $data['preview']['consumed']['reference']);
        $this->assertSame(0, $data['preview']['merged_participant_count']);
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\EntriesController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/entries/add', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Controller Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'controller@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame(1, $entries->find()->where(['entry_name' => 'Controller Entry'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\EntriesController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/entries/edit/' . self::FIXTURE_ENTRY_ID, [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Updated Entry',
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 1,
            'entry_email' => 'updated@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame('Updated Entry', $entries->get(self::FIXTURE_ENTRY_ID)->entry_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\EntriesController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/entries/delete/' . self::FIXTURE_ENTRY_ID);

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $deleted = $entries->find('withTrashed')->where(['id' => self::FIXTURE_ENTRY_ID])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::merge()
     */
    public function testMergeMovesParticipantsAndRedirectsToSurvivingEntry(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $entries = $this->getTableLocator()->get('Entries');
        $participants = $this->getTableLocator()->get('Participants');

        $event = $events->newEntity([
            'event_name' => 'Merge Test Event',
            'event_description' => 'Mergeable entries event',
            'booking_code' => 'MERGE',
            'start_time' => '2027-01-01 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);
        $this->assertNotFalse($events->save($event));

        $survivor = $this->createSearchableEntry('MERGE', 51, false, $event->id);
        $victim = $this->createSearchableEntry('MERGE', 52, false, $event->id);

        $participant = $participants->newEntity([
            'first_name' => 'Merge',
            'last_name' => 'Victim',
            'entry_id' => $victim->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->assertNotFalse($participants->save($participant));

        $this->enableFormTokens();
        $this->post('/entries/merge/' . $victim->id, [
            'persisting_entry_id' => $survivor->id,
        ]);

        $this->assertResponseOk();
        $this->assertResponseContains('Final confirmation required.');
        $this->assertResponseContains('Confirm Merge');

        $this->enableFormTokens();
        $this->post('/entries/merge/' . $victim->id . '/' . $survivor->id, [
            'persisting_entry_id' => $survivor->id,
            'confirmed' => 1,
        ]);

        $this->assertRedirect(['controller' => 'Entries', 'action' => 'view', $survivor->id]);
        $this->assertSame(1, $participants->find()->where(['entry_id' => $survivor->id])->count());
        $deleted = $entries->find('withTrashed')->where(['id' => $victim->id])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testLookupOptions(): void
    {
        $this->configRequest([
            'headers' => [
                'Origin' => 'http://localhost',
                'Access-Control-Request-Method' => 'POST',
            ],
        ]);
        $this->options('/lookup.json');

        $this->assertResponseOk();
        $this->assertResponseContains('OPTIONS YES');
        $this->assertHeader('Access-Control-Allow-Origin', '*');
        $this->assertHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->assertHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token');
    }

    public function testJsonViewOptions(): void
    {
        $this->configRequest([
            'headers' => [
                'Origin' => 'http://localhost',
                'Access-Control-Request-Method' => 'GET',
            ],
        ]);
        $this->options('/entries/view/' . self::FIXTURE_ENTRY_ID . '.json');

        $this->assertResponseOk();
        $this->assertResponseContains('OPTIONS YES');
        $this->assertHeader('Access-Control-Allow-Origin', '*');
        $this->assertHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->assertHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token');
    }

    public function testLookupRejectsInvalidData(): void
    {
        $this->post('/lookup.json', [
            'reference_number' => 'abc',
            'security_code' => 'Lor',
        ]);

        $this->assertResponseCode(400);
        $this->assertResponseContains('Invalid Lookup Data');
    }

    public function testLookupReturnsEntry(): void
    {
        [$entry, $participant] = $this->createPublicEntryWithParticipant();

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => $entry->security_code,
        ]);

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertPublicEntryResponse($data, $entry->id, (int)$entry->reference_number, $participant->id);
    }

    public function testLookupReturnsNotFoundForWrongSecurityCode(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Lookup Failure Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'lookup-fail@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $this->assertNotFalse($entries->save($entry));

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => 'WRONG',
        ]);

        $this->assertResponseCode(404);
        $this->assertResponseContains('Invalid Lookup');
    }

    public function testLookupJsonIsPublic(): void
    {
        [$entry, $participant] = $this->createPublicEntryWithParticipant();
        $this->session([]);

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => $entry->security_code,
        ]);

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertPublicEntryResponse($data, $entry->id, (int)$entry->reference_number, $participant->id);
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::findByReference()
     */
    public function testFindByReferenceRedirectsUsingFullReference(): void
    {
        $entry = $this->createSearchableEntry('SEARCH1', 42, true);

        $this->get('/entries/find-by-reference?reference=SEARCH1-42');

        $this->assertRedirect(['controller' => 'Entries', 'action' => 'view', $entry->id]);
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::findByReference()
     */
    public function testFindByReferenceRedirectsUsingNumericReferenceForActiveEvent(): void
    {
        $entry = $this->createSearchableEntry('SEARCH2', 77, true);

        $this->get('/entries/find-by-reference?reference=77');

        $this->assertRedirect(['controller' => 'Entries', 'action' => 'view', $entry->id]);
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::findByReference()
     */
    public function testFindByReferenceRejectsInvalidFormat(): void
    {
        $this->get('/entries/find-by-reference?reference=not-a-reference');

        $this->assertRedirect(['controller' => 'Events', 'action' => 'current']);
        $this->assertFlashMessage('Entry references must look like BOOKINGCODE-123 or just 123.');
    }

    /**
     * @return void
     * @uses \App\Controller\EntriesController::findByReference()
     */
    public function testFindByReferenceRequiresFullReferenceWhenNumericMatchIsAmbiguous(): void
    {
        $this->createSearchableEntry('AMBIG1', 91, false);
        $this->createSearchableEntry('AMBIG2', 91, false);

        $this->get('/entries/find-by-reference?reference=91');

        $this->assertRedirect(['controller' => 'Events', 'action' => 'current']);
        $this->assertFlashMessage('Reference 91 matches multiple events. Use the full reference code.');
    }

    public function testJsonViewIsPublicAndMatchesLookupSignature(): void
    {
        [$entry, $participant] = $this->createPublicEntryWithParticipant();
        $this->session([]);

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => $entry->security_code,
        ]);
        $lookupData = json_decode((string)$this->_response->getBody(), true);

        $this->get('/entries/view/' . $entry->id . '.json');

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertPublicEntryResponse($data, $entry->id, (int)$entry->reference_number, $participant->id);
        $this->assertSame($lookupData, $data);
    }

    /**
     * @return array{0: \App\Model\Entity\Entry, 1: \App\Model\Entity\Participant}
     */
    private function createPublicEntryWithParticipant(): array
    {
        $entries = $this->getTableLocator()->get('Entries');
        $participants = $this->getTableLocator()->get('Participants');

        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Lookup Entry',
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'entry_email' => 'lookup@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $this->assertNotFalse($entries->save($entry));

        /** @var \App\Model\Entity\Participant $participant */
        $participant = $participants->newEntity([
            'first_name' => 'Lookup',
            'last_name' => 'Participant',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->assertNotFalse($participants->save($participant));

        return [$entry, $participant];
    }

    /**
     * @param string $bookingCode
     * @param int $referenceNumber
     * @param bool $bookable
     * @param string|null $eventId
     * @return \App\Model\Entity\Entry
     */
    private function createSearchableEntry(
        string $bookingCode,
        int $referenceNumber,
        bool $bookable,
        ?string $eventId = null,
    ): Entry {
        $events = $this->getTableLocator()->get('Events');
        $entries = $this->getTableLocator()->get('Entries');

        if ($eventId === null) {
            /** @var \App\Model\Entity\Event $event */
            $event = $events->newEntity([
                'event_name' => 'Search Event ' . $bookingCode,
                'event_description' => 'Searchable event',
                'booking_code' => $bookingCode,
                'start_time' => '2027-01-01 10:00:00',
                'bookable' => $bookable,
                'finished' => false,
                'entry_count' => 0,
                'participant_count' => 0,
                'checked_in_count' => 0,
            ]);
            $this->assertNotFalse($events->save($event));
            $eventId = $event->id;
        }

        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->newEntity([
            'event_id' => $eventId,
            'entry_name' => 'Search Entry ' . $bookingCode . ' ' . $referenceNumber,
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => strtolower($bookingCode) . $referenceNumber . '@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $this->assertNotFalse($entries->save($entry));

        $entries->updateAll(['reference_number' => $referenceNumber], ['id' => $entry->id]);
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->get($entry->id);

        return $entry;
    }

    /**
     * @param array<string, mixed> $data
     * @param string $entryId
     * @param int $referenceNumber
     * @param string $participantId
     * @return void
     */
    private function assertPublicEntryResponse(
        array $data,
        string $entryId,
        int $referenceNumber,
        string $participantId,
    ): void {
        $this->assertArrayHasKey('entry', $data);
        $this->assertSame($entryId, $data['entry']['id']);
        $this->assertSame('Lookup Entry', $data['entry']['entry_name']);
        $this->assertSame($referenceNumber, $data['entry']['reference_number']);
        $this->assertSame(1, $data['entry']['participant_count']);
        $this->assertArrayNotHasKey('security_code', $data['entry']);
        $this->assertArrayNotHasKey('entry_email', $data['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $data['entry']);
        $this->assertArrayNotHasKey('active', $data['entry']);
        $this->assertArrayNotHasKey('deleted', $data['entry']);
        $this->assertCount(1, $data['entry']['participants']);
        $this->assertSame($participantId, $data['entry']['participants'][0]['id']);
        $this->assertSame('Lookup', $data['entry']['participants'][0]['first_name']);
        $this->assertSame('Participant', $data['entry']['participants'][0]['last_name']);
        $this->assertSame('Lookup  Participant', $data['entry']['participants'][0]['full_name']);
        $this->assertArrayHasKey('participant_type', $data['entry']['participants'][0]);
        $this->assertSame(
            'Lorem ipsum dolor sit amet',
            $data['entry']['participants'][0]['participant_type']['participant_type'],
        );
        $this->assertArrayHasKey('section', $data['entry']['participants'][0]);
        $this->assertSame(
            'Lorem ipsum dolor sit amet',
            $data['entry']['participants'][0]['section']['section_name'],
        );
    }
}
