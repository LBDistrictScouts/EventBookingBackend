<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\View\Exception\MissingTemplateException;

/**
 * App\Controller\EventsController Test Case
 *
 * @uses \App\Controller\EventsController
 */
class BookingControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EventsController::index()
     */
    public function testBook(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->first();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->first();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->first();

        $this->post(
            '/book.json',
            [
                'event_id' => $event->id,
                'entry_name' => 'Test My Booking',
                'entry_email' => 'jacob@this.com',
                'entry_mobile' => '08938 928197',
                'participants' => [
                    [
                        'access_key' => '2a235073-63c3-42fe-a12d-5429b21562e6',
                        'first_name' => 'Jacob',
                        'last_name' => 'Tyler',
                        'participant_type_id' => $participantType->id,
                        'section_id' => $section->id,
                    ],
                ],
            ],
        );

        $this->assertResponseOk();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EventsController::index()
     */
    public function testBlankBook(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->first();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->first();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->first();

        $requestData = [
            'event_id' => $event->id,
            'entry_name' => 'Test My Booking',
            'entry_email' => 'jacob@this.com',
            'entry_mobile' => '08938 928197',
            'participants' => [
                [
                    'access_key' => '2a235073-63c3-42fe-a12d-5429b21562e6',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                ],
                [
                    'access_key' => 'bac8cfbf-d59a-49db-8d81-de424ba60f8e',
                    'first_name' => 'Joe',
                    'last_name' => 'Bloggs',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                    '' => '9aebb3ae-8fd6-40f5-be2b-3e823a8a8ca6',
                ],
                [
                    'access_key' => '02310d8b-3fe0-47dd-a5e6-b02412a4bcbc',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                    '' => '9aebb3ae-8fd6-40f5-be2b-3e823a8a8ca6',
                ],
            ],
        ];

        $this->post('/book.json', $requestData);
        $this->assertResponseOk();

        $resultData = json_decode($this->_response->getBody()->__toString(), true);

        $expected = [
            'success' => true,
            'entry' => $requestData,
            'message' => 'Saved',
        ];

        $this->assertEmpty($resultData['errors']);
        $this->assertSame($expected['success'], $resultData['success']);
        $this->assertSame($expected['message'], $resultData['message']);

        $this->assertCount(3, $resultData['entry']['participants']);
        $this->assertCount(16, $resultData['entry']);

        $this->assertEquals($expected['entry']['entry_name'], $resultData['entry']['entry_name']);
        $this->assertEquals($expected['entry']['entry_email'], $resultData['entry']['entry_email']);
        $this->assertEquals($expected['entry']['entry_mobile'], $resultData['entry']['entry_mobile']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EventsController::view()
     */
    public function testView(): void
    {
        $this->disableErrorHandlerMiddleware();
        $this->expectException(MissingTemplateException::class);
        $this->get('/booking/view/2342ad37-13f0-4fd1-bd3f-2032273626ce');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\EventsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->put('/book.json', []);
        $this->assertResponseCode(400);
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\EventsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/booking/edit/2342ad37-13f0-4fd1-bd3f-2032273626ce', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Booking Edit',
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 1,
            'entry_email' => 'edited@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);

        $this->assertResponseOk();

        $resultData = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame(true, $resultData['success']);
        $this->assertSame('Saved', $resultData['message']);
        $this->assertSame([], $resultData['errors']);
        $this->assertSame('2342ad37-13f0-4fd1-bd3f-2032273626ce', $resultData['entry']['id']);
        $this->assertSame('Booking Edit', $resultData['entry']['entry_name']);
        $this->assertArrayNotHasKey('security_code', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_email', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $resultData['entry']);
        $this->assertArrayNotHasKey('active', $resultData['entry']);
        $this->assertArrayNotHasKey('deleted', $resultData['entry']);

        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame('Booking Edit', $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce')->entry_name);
    }

    /**
     * @return void
     */
    public function testEditSoftDeletesParticipantsMissingFromRequest(): void
    {
        $this->enableFormTokens();
        $this->post('/booking/edit/2342ad37-13f0-4fd1-bd3f-2032273626ce', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Booking Edit',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'edited@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
            'participants' => [],
        ]);

        $this->assertResponseOk();

        $resultData = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame(true, $resultData['success']);
        $this->assertSame('Saved', $resultData['message']);
        $this->assertSame([], $resultData['errors']);
        $this->assertCount(0, $resultData['entry']['participants']);

        $participants = $this->getTableLocator()->get('Participants');
        $deleted = $participants->find('withTrashed')
            ->where(['id' => '5045fd83-55db-4d36-8a8a-63222e50e3fd'])
            ->firstOrFail();

        $this->assertNotNull($deleted->deleted);
    }

    /**
     * @return void
     */
    public function testEditJsonDoesNotRequireCsrfToken(): void
    {
        $this->post('/booking/edit/2342ad37-13f0-4fd1-bd3f-2032273626ce.json', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Booking Edit Json',
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 1,
            'entry_email' => 'edited@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);

        $this->assertResponseOk();

        $resultData = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame(true, $resultData['success']);
        $this->assertSame('Saved', $resultData['message']);
        $this->assertSame([], $resultData['errors']);
        $this->assertSame('Booking Edit Json', $resultData['entry']['entry_name']);
        $this->assertArrayNotHasKey('security_code', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_email', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $resultData['entry']);

        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame('Booking Edit Json', $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce')->entry_name);
    }

    /**
     * @return void
     */
    public function testPublicEntrySignatureMatchesAcrossLookupViewAndBookingEdit(): void
    {
        [$entry, $participant] = $this->createPublicEntryWithParticipant();
        $this->session([]);

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => $entry->security_code,
        ]);
        $this->assertResponseOk();
        $lookupData = json_decode((string)$this->_response->getBody(), true);

        $this->get('/entries/view/' . $entry->id . '.json');
        $this->assertResponseOk();
        $viewData = json_decode((string)$this->_response->getBody(), true);

        $this->post('/booking/edit/' . $entry->id . '.json', [
            'event_id' => $entry->event_id,
            'entry_name' => $entry->entry_name,
            'entry_email' => $entry->entry_email,
            'entry_mobile' => $entry->entry_mobile,
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'security_code' => $entry->security_code,
        ]);
        $this->assertResponseOk();
        $editData = json_decode((string)$this->_response->getBody(), true);

        $this->assertArrayHasKey('entry', $lookupData);
        $this->assertArrayHasKey('entry', $viewData);
        $this->assertArrayHasKey('entry', $editData);

        $this->assertSame($lookupData['entry'], $viewData['entry']);
        $this->assertSame($lookupData['entry'], $editData['entry']);

        $this->assertSame($entry->id, $editData['entry']['id']);
        $this->assertSame($entry->entry_name, $editData['entry']['entry_name']);
        $this->assertSame((int)$entry->reference_number, $editData['entry']['reference_number']);
        $this->assertSame(1, $editData['entry']['participant_count']);
        $this->assertSame(0, $editData['entry']['checked_in_count']);
        $this->assertCount(1, $editData['entry']['participants']);
        $this->assertSame($participant->id, $editData['entry']['participants'][0]['id']);

        $this->assertArrayNotHasKey('security_code', $editData['entry']);
        $this->assertArrayNotHasKey('entry_email', $editData['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $editData['entry']);
        $this->assertArrayNotHasKey('active', $editData['entry']);
        $this->assertArrayNotHasKey('deleted', $editData['entry']);
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

}
