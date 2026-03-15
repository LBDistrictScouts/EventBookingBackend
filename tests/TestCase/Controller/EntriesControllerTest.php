<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

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
    }
}
