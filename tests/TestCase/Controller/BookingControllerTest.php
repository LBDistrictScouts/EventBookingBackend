<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\View\Exception\MissingTemplateException;
use Opis\JsonSchema\Validator;

/**
 * App\Controller\EventsController Test Case
 *
 * @uses \App\Controller\EventsController
 */
class BookingControllerTest extends TestCase
{
    use EmailTrait;
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

    public function testBookJsonDoesNotRequireAuthentication(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->firstOrFail();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->firstOrFail();

        $this->session([]);

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Public Booking',
            'entry_email' => 'public-booking@example.com',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => 'ab3437be-53f2-49f8-af5f-c8f1daebcb91',
                    'first_name' => 'Public',
                    'last_name' => 'Booker',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                ],
            ],
        ]);

        $this->assertResponseOk();
        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 1);
    }

    public function testBookResolvesParentVolunteerAliasToParticipantTypeId(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();
        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');

        $parentVolunteerType = $participantTypes->newEntity([
            'id' => '7474361d-03ff-44fc-9a02-388e6c0d688c',
            'participant_type' => 'Parent / Non Uniformed Volunteer',
            'adult' => true,
            'uniformed' => false,
            'out_of_district' => false,
            'category' => 1,
            'sort_order' => 12,
            'osm_type_code' => null,
        ]);
        $parentVolunteerType = $participantTypes->saveOrFail($parentVolunteerType);

        $this->session([]);

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Parent Alias Booking',
            'entry_email' => 'parent-alias@example.com',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => '4ed9cd92-72c8-4157-aa91-6c0f37577b69',
                    'first_name' => 'Taylor',
                    'last_name' => 'Helper',
                    'participant_type_id' => 'Parents / non-uniformed volunteers',
                ],
            ],
        ]);

        $this->assertResponseOk();
        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 1);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->find()
            ->where(['entry_email' => 'parent-alias@example.com'])
            ->contain(['Participants'])
            ->firstOrFail();

        $this->assertCount(1, $entry->participants);
        $this->assertSame(
            $parentVolunteerType->id,
            $entry->participants[0]->participant_type_id,
        );
        $this->assertNull($entry->participants[0]->section_id);
    }

    public function testBookReturnsAllParticipantsInSerializedResponse(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->firstOrFail();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->firstOrFail();

        $this->session([]);

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Two Participant Booking',
            'entry_email' => 'two-participant@example.com',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => '26644a91-1fe0-495a-9b1b-4719b79df5ba',
                    'first_name' => 'Joseph',
                    'last_name' => 'Walker',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                ],
                [
                    'access_key' => '6f882fd2-3ff4-446b-a4fc-444b9881ad44',
                    'first_name' => 'Gemma',
                    'last_name' => 'Walker',
                    'participant_type_id' => $participantType->id,
                ],
            ],
        ]);

        $this->assertResponseOk();
        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 2);
        $this->assertSame(['Gemma', 'Joseph'], array_column($resultData['entry']['participants'], 'first_name'));
        $this->assertArrayHasKey('participant_type', $resultData['entry']['participants'][0]);
        $this->assertArrayHasKey('section', $resultData['entry']['participants'][0]);
        $this->assertNull($resultData['entry']['participants'][0]['section']);
        $this->assertArrayHasKey('section', $resultData['entry']['participants'][1]);
        $this->assertSame($section->id, $resultData['entry']['participants'][1]['section']['id']);
    }

    public function testBookValidationFailureReturnsStructuredErrors(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();

        $this->session([]);

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Broken Booking',
            'entry_email' => 'not-an-email',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => '6d5faab8-c6df-47fd-ac07-ddefa1dd4950',
                    'first_name' => 'Taylor',
                    'last_name' => 'Helper',
                    'participant_type_id' => '',
                ],
            ],
        ]);

        $this->assertResponseCode(400);
        $resultData = $this->decodeJsonResponse();
        $this->assertFailedBookingResponse($resultData);
        $this->assertArrayHasKey('entry_email', $resultData['errors']);
        $this->assertArrayHasKey('participants', $resultData['errors']);
        $this->assertNotEmpty($resultData['errors']['entry_email']);
        $this->assertNotEmpty($resultData['errors']['participants'][0]['participant_type_id']);
    }

    public function testBookSendsSectionNotificationEmailsWithFullTeamRoster(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->firstOrFail();

        $sections = $this->getTableLocator()->get('Sections');
        $firstSection = $sections->get('95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $secondSection = $sections->newEntity([
            'section_name' => 'Different Section',
            'notification_email' => 'different-section@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 99,
        ]);
        $this->assertNotFalse($sections->save($secondSection));

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Mixed Team',
            'entry_email' => 'mixed-team@example.com',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => 'ab3437be-53f2-49f8-af5f-c8f1daebcb91',
                    'first_name' => 'Alex',
                    'last_name' => 'Walker',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $firstSection->id,
                ],
                [
                    'access_key' => '5f6e69f2-fc75-4d1d-b6cb-7176d9e620d4',
                    'first_name' => 'Sam',
                    'last_name' => 'Rivers',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $secondSection->id,
                ],
                [
                    'access_key' => 'fe16ca0b-5fcb-469d-a8a1-7d16f58ac8a8',
                    'first_name' => 'Pat',
                    'last_name' => 'Jones',
                    'participant_type_id' => $participantType->id,
                ],
            ],
        ]);

        $this->assertResponseOk();
        $this->assertMailCount(3);
        $this->assertMailSentTo('mixed-team@example.com');
        $this->assertMailSentTo('section@example.com');
        $this->assertMailSentTo('different-section@example.com');
        $this->assertMailContainsTextAt(1, 'Alex Walker');
        $this->assertMailContainsTextAt(1, 'Sam Rivers');
        $this->assertMailContainsTextAt(1, 'Section: Different Section');
        $this->assertMailContainsTextAt(2, 'Alex Walker');
        $this->assertMailContainsTextAt(2, 'Sam Rivers');
    }

    public function testBookDeduplicatesSectionNotificationsByEmailAddress(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->firstOrFail();

        $sections = $this->getTableLocator()->get('Sections');
        $firstSection = $sections->get('95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $secondSection = $sections->newEntity([
            'section_name' => 'Shared Inbox Section',
            'notification_email' => 'shared-inbox@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 100,
        ]);
        $this->assertNotFalse($sections->save($secondSection));

        $firstSection->notification_email = 'shared-inbox@example.com';
        $this->assertNotFalse($sections->save($firstSection));

        $this->post('/book.json', [
            'event_id' => $event->id,
            'entry_name' => 'Shared Inbox Team',
            'entry_email' => 'shared-team@example.com',
            'entry_mobile' => '07123456780',
            'participants' => [
                [
                    'access_key' => 'd53ab523-0f37-4b1b-b4e0-167fdad4431c',
                    'first_name' => 'Alex',
                    'last_name' => 'Walker',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $firstSection->id,
                ],
                [
                    'access_key' => '7df1d2cb-6331-42b0-97cc-1626f13c2f8e',
                    'first_name' => 'Sam',
                    'last_name' => 'Rivers',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $secondSection->id,
                ],
            ],
        ]);

        $this->assertResponseOk();
        $this->assertMailCount(2);
        $this->assertMailSentToAt(0, 'shared-team@example.com');
        $this->assertMailSentToAt(1, 'shared-inbox@example.com');
        $this->assertMailSubjectContains('New Signup for Lorem ipsum dolor sit amet & Shared Inbox Section');
        $this->assertMailContainsTextAt(1, 'Alex Walker');
        $this->assertMailContainsTextAt(1, 'Sam Rivers');
        $this->assertMailContainsTextAt(1, 'A new booking has been received for Lorem ipsum dolor sit amet & Shared Inbox Section.');
        $this->assertMailContainsTextAt(1, 'Participants: 2 total, 1 from Lorem ipsum dolor sit amet, 1 from Shared Inbox Section.');
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

        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 3);

        $expected = [
            'success' => true,
            'entry' => $requestData,
            'message' => 'Saved',
        ];

        $this->assertSame($expected['success'], $resultData['success']);
        $this->assertSame($expected['message'], $resultData['message']);

        $this->assertCount(3, $resultData['entry']['participants']);
        $this->assertCount(17, $resultData['entry']);

        $this->assertEquals($expected['entry']['entry_name'], $resultData['entry']['entry_name']);
        $this->assertEquals($expected['entry']['entry_email'], $resultData['entry']['entry_email']);
        $this->assertEquals($expected['entry']['entry_mobile'], $resultData['entry']['entry_mobile']);
        $this->assertArrayHasKey('participant_type', $resultData['entry']['participants'][0]);
        $this->assertArrayHasKey('section', $resultData['entry']['participants'][0]);
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

        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 1);
        $this->assertSame('2342ad37-13f0-4fd1-bd3f-2032273626ce', $resultData['entry']['id']);
        $this->assertSame('Booking Edit', $resultData['entry']['entry_name']);
        $this->assertArrayNotHasKey('security_code', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_email', $resultData['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $resultData['entry']);
        $this->assertArrayNotHasKey('active', $resultData['entry']);
        $this->assertArrayNotHasKey('deleted', $resultData['entry']);

        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame('Booking Edit', $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce')->entry_name);
        $this->assertMailCount(1);
        $this->assertMailSentTo('edited@example.com');
        $this->assertMailSubjectContains('Booking Update');
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

        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 0);
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

        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 1);
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
    public function testEditReusesParticipantMatchedByAccessKeyWithoutId(): void
    {
        $participantAccessKey = '391f7123-14fe-4f63-8fc1-a7c6a2aebfb2';
        [$entry, $participant] = $this->createPublicEntryWithParticipant($participantAccessKey);
        $participantId = $participant->id;

        $this->post('/booking/edit/' . $entry->id . '.json', [
            'event_id' => $entry->event_id,
            'entry_name' => 'Booking Edit Json',
            'entry_email' => $entry->entry_email,
            'entry_mobile' => $entry->entry_mobile,
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'security_code' => $entry->security_code,
            'participants' => [
                [
                    'access_key' => $participantAccessKey,
                    'first_name' => 'Jacob',
                    'last_name' => 'Updated',
                    'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
                    'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
                ],
            ],
        ]);

        $this->assertResponseOk();

        $resultData = $this->decodeJsonResponse();
        $this->assertSuccessfulBookingResponse($resultData, 1);
        $this->assertSame($participantId, $resultData['entry']['participants'][0]['id']);
        $this->assertSame('Jacob', $resultData['entry']['participants'][0]['first_name']);
        $this->assertSame('Updated', $resultData['entry']['participants'][0]['last_name']);

        $participants = $this->getTableLocator()->get('Participants');
        $entryParticipants = $participants->find('withTrashed')
            ->where(['entry_id' => $entry->id])
            ->all()
            ->toList();

        $this->assertCount(1, $entryParticipants);
        $this->assertSame($participantId, $entryParticipants[0]->id);
        $this->assertSame($participantAccessKey, $entryParticipants[0]->access_key);
        $this->assertNull($entryParticipants[0]->deleted);
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
        $this->assertArrayHasKey('participant_type', $editData['entry']['participants'][0]);
        $this->assertSame(
            'Lorem ipsum dolor sit amet',
            $editData['entry']['participants'][0]['participant_type']['participant_type'],
        );
        $this->assertArrayHasKey('section', $editData['entry']['participants'][0]);
        $this->assertSame(
            'Lorem ipsum dolor sit amet',
            $editData['entry']['participants'][0]['section']['section_name'],
        );

        $this->assertArrayNotHasKey('security_code', $editData['entry']);
        $this->assertArrayNotHasKey('entry_email', $editData['entry']);
        $this->assertArrayNotHasKey('entry_mobile', $editData['entry']);
        $this->assertArrayNotHasKey('active', $editData['entry']);
        $this->assertArrayNotHasKey('deleted', $editData['entry']);
    }

    /**
     * @param string|null $participantAccessKey
     * @return array{0: \App\Model\Entity\Entry, 1: \App\Model\Entity\Participant}
     */
    private function createPublicEntryWithParticipant(?string $participantAccessKey = null): array
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
            'access_key' => $participantAccessKey,
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
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(): array
    {
        /** @var array<string, mixed> $data */
        $data = json_decode((string)$this->_response->getBody(), true);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @param int $expectedParticipantCount
     * @return void
     */
    private function assertSuccessfulBookingResponse(array $data, int $expectedParticipantCount): void
    {
        $this->assertBookingResponseMatchesSchema(
            $data,
            'https://greenway.lbdscouts.org.uk/booking-response-schema.json',
            'config/schema/booking-response-schema.json',
        );
        $this->assertSame(true, $data['success']);
        $this->assertSame('Saved', $data['message']);
        $this->assertSame([], $data['errors']);
        $this->assertArrayHasKey('entry', $data);
        $this->assertIsArray($data['entry']);
        $this->assertSame($expectedParticipantCount, $data['entry']['participant_count']);
        $this->assertCount($expectedParticipantCount, $data['entry']['participants']);
    }

    /**
     * @param array<string, mixed> $data
     * @return void
     */
    private function assertFailedBookingResponse(array $data): void
    {
        $this->assertBookingResponseMatchesSchema(
            $data,
            'https://greenway.lbdscouts.org.uk/booking-response-schema.json',
            'config/schema/booking-response-schema.json',
        );
        $this->assertSame(false, $data['success']);
        $this->assertSame('Error', $data['message']);
        $this->assertIsArray($data['errors']);
        $this->assertNotEmpty($data['errors']);
    }

    /**
     * @param array<string, mixed> $data
     * @param string $schemaId
     * @param string $schemaFile
     * @return void
     */
    private function assertBookingResponseMatchesSchema(array $data, string $schemaId, string $schemaFile): void
    {
        $validator = new Validator();
        $validator->resolver()->registerFile(
            $schemaId,
            dirname(__DIR__, 3) . '/' . ltrim($schemaFile, '/'),
        );

        $result = $validator->validate(
            json_decode((string)json_encode($data, JSON_THROW_ON_ERROR)),
            $schemaId,
        );
        $this->assertTrue($result->isValid(), (string)json_encode($result->error(), JSON_THROW_ON_ERROR));
    }
}
