<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SectionsController Test Case
 *
 * @uses \App\Controller\SectionsController
 */
class SectionsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\SectionsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/sections/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('sections', $data);
        $this->assertCount(1, $data['sections']);
        $this->assertArrayNotHasKey('notification_email', $data['sections'][0]);
    }

    public function testPublicIndexJsonDoesNotRequireAuthentication(): void
    {
        $this->session([]);

        $this->get('/sections.json');

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('sections', $data);
        $this->assertCount(1, $data['sections']);
        $this->assertArrayNotHasKey('notification_email', $data['sections'][0]);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\SectionsController::view()
     */
    public function testView(): void
    {
        $this->get('/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Checkpoint Progress');
        $this->assertResponseContains('1 participants');
    }

    public function testViewCheckpointProgressFollowsCurrentEventFilter(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Second Event',
            'event_description' => 'Second Event Description',
            'booking_code' => 'SECOND',
            'start_time' => '2027-01-16 12:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 1,
            'checked_in_count' => 0,
        ]);
        $events->saveOrFail($event);
        $eventId = (string)$event->id;

        $eventsSections = $this->getTableLocator()->get('EventsSections');
        $eventsSection = $eventsSections->newEntity([
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'event_id' => $eventId,
        ], [
            'accessibleFields' => [
                'section_id' => true,
                'event_id' => true,
            ],
        ]);
        $eventsSections->saveOrFail($eventsSection);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $checkpoint = $checkpoints->newEntity([
            'id' => 'c1111111-a2f3-4775-b75d-1fd3e57cc4b7',
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Second Event Summit',
            'event_id' => $eventId,
        ]);
        $checkpoints->saveOrFail($checkpoint);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $eventId,
            'entry_name' => 'Second Event Team',
            'reference_number' => 2,
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'entry_email' => 'second@example.com',
            'entry_mobile' => '07000000000',
            'security_code' => 'XYZ12',
        ]);
        $entries->saveOrFail($entry);
        $entryId = (string)$entry->id;

        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->newEntity([
            'first_name' => 'Second',
            'last_name' => 'Walker',
            'entry_id' => $entryId,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $participants->saveOrFail($participant);

        $this->get('/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1');
        $this->assertResponseOk();
        $this->assertResponseNotContains('Second Event Summit');

        $this->get('/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1?all=1');
        $this->assertResponseOk();
        $this->assertResponseContains('Second Event Summit');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\SectionsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/sections/add', [
            'section_name' => 'Explorers',
            'notification_email' => 'explorers@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => '',
            'events' => [
                '_ids' => [
                    '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                ],
            ],
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->get(
            $sections->find()->select(['id'])->where(['section_name' => 'Explorers'])->firstOrFail()->id,
            contain: ['Events'],
        );
        $this->assertSame('explorers@example.com', $section->notification_email);
        $this->assertNull($section->osm_section_id);
        $this->assertCount(1, $section->events);
        $this->assertSame('3a6d9419-b621-45cf-a13e-4db9647bf5bc', $section->events[0]->id);
    }

    public function testAddFormRendersNotificationEmailAndOsmSectionIdInputs(): void
    {
        $this->get('/sections/add');

        $this->assertResponseOk();
        $this->assertResponseContains('name="notification_email"');
        $this->assertResponseContains('type="email"');
        $this->assertResponseContains('name="osm_section_id"');
        $this->assertResponseContains('type="number"');
        $this->assertResponseNotContains('<select name="osm_section_id"');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\SectionsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/sections/edit/95116a77-0675-4e1a-9d0c-74e3d40d92c1', [
            'section_name' => 'Renamed Section',
            'notification_email' => 'renamed@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => '',
            'events' => [
                '_ids' => [
                    '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                ],
            ],
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->get('95116a77-0675-4e1a-9d0c-74e3d40d92c1', contain: ['Events']);
        $this->assertSame('Renamed Section', $section->section_name);
        $this->assertSame('renamed@example.com', $section->notification_email);
        $this->assertNull($section->osm_section_id);
        $this->assertCount(1, $section->events);
        $this->assertSame('3a6d9419-b621-45cf-a13e-4db9647bf5bc', $section->events[0]->id);
    }

    public function testEditFormRendersCurrentNotificationEmailAndOsmSectionIdInputs(): void
    {
        $this->get('/sections/edit/95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertResponseOk();
        $this->assertResponseContains('name="notification_email"');
        $this->assertResponseContains('value="section@example.com"');
        $this->assertResponseContains('name="osm_section_id"');
        $this->assertResponseContains('type="number"');
        $this->assertResponseNotContains('<select name="osm_section_id"');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\SectionsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/sections/delete/95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $deleted = $sections->find('withTrashed')->where(['id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
