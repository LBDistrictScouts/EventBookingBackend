<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\GroupsController Test Case
 *
 * @uses \App\Controller\GroupsController
 */
class GroupsControllerTest extends TestCase
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
        'app.Entries',
        'app.Participants',
        'app.Checkpoints',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\GroupsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/groups/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('groups', $data);
        $this->assertCount(1, $data['groups']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\GroupsController::view()
     */
    public function testView(): void
    {
        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Checkpoint Progress');
        $this->assertResponseContains('1 participants');
    }

    public function testViewCheckpointProgressFollowsCurrentEventFilter(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Second Group Event',
            'event_description' => 'Second Group Event Description',
            'booking_code' => 'GROUP2',
            'start_time' => '2027-01-17 12:00:00',
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
            'id' => 'c2222222-a2f3-4775-b75d-1fd3e57cc4b7',
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Group Event Summit',
            'event_id' => $eventId,
        ]);
        $checkpoints->saveOrFail($checkpoint);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $eventId,
            'entry_name' => 'Second Group Team',
            'reference_number' => 2,
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'entry_email' => 'group@example.com',
            'entry_mobile' => '07000000001',
            'security_code' => 'XYZ34',
        ]);
        $entries->saveOrFail($entry);
        $entryId = (string)$entry->id;

        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->newEntity([
            'first_name' => 'Group',
            'last_name' => 'Walker',
            'entry_id' => $entryId,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $participants->saveOrFail($participant);

        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64');
        $this->assertResponseOk();
        $this->assertResponseNotContains('Group Event Summit');

        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64?all=1');
        $this->assertResponseOk();
        $this->assertResponseContains('Group Event Summit');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\GroupsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/groups/add', [
            'group_name' => 'Integration Group',
            'visible' => true,
            'sort_order' => 2,
        ]);

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $this->assertSame(1, $groups->find()->where(['group_name' => 'Integration Group'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\GroupsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/groups/edit/873b0f71-5389-46f9-baae-7d4855406b64', [
            'group_name' => 'Renamed Group',
            'visible' => true,
            'sort_order' => 3,
        ]);

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $this->assertSame('Renamed Group', $groups->get('873b0f71-5389-46f9-baae-7d4855406b64')->group_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\GroupsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/groups/delete/873b0f71-5389-46f9-baae-7d4855406b64');

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $deleted = $groups->find('withTrashed')->where(['id' => '873b0f71-5389-46f9-baae-7d4855406b64'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testViewWithBillingQuery(): void
    {
        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64?event_id=3a6d9419-b621-45cf-a13e-4db9647bf5bc');

        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }
}
