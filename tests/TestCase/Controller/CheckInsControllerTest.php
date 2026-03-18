<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\View\Exception\MissingTemplateException;

/**
 * App\Controller\CheckInsController Test Case
 *
 * @uses \App\Controller\CheckInsController
 */
class CheckInsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/check-ins');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::view()
     */
    public function testView(): void
    {
        $this->get('/check-ins/view/2172aa66-e48c-4026-aa73-e6674a3d9926');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/check-in.json', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2025-01-16 12:00:00',
            'participants' => ['5045fd83-55db-4d36-8a8a-63222e50e3fd'],
        ]);

        $this->assertResponseOk();
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $this->assertGreaterThan(1, $checkIns->find()->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/check-ins/edit/2172aa66-e48c-4026-aa73-e6674a3d9926', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2025-01-16 13:00:00',
            'participant_count' => 1,
        ]);

        $this->assertRedirectContains('/check-ins');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        /** @var \App\Model\Entity\CheckIn $checkIn */
        $checkIn = $checkIns->get('2172aa66-e48c-4026-aa73-e6674a3d9926');
        $this->assertSame('2025-01-16 13:00:00', $checkIn->check_in_time->format('Y-m-d H:i:s'));
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/check-ins/delete/2172aa66-e48c-4026-aa73-e6674a3d9926');

        $this->assertRedirectContains('/check-ins');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $deleted = $checkIns->find('withTrashed')->where(['id' => '2172aa66-e48c-4026-aa73-e6674a3d9926'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testAddOptionsRequest(): void
    {
        $this->configRequest([
            'headers' => [
                'Origin' => 'http://localhost',
                'Access-Control-Request-Method' => 'POST',
            ],
        ]);
        $this->options('/check-in.json');

        $this->assertResponseOk();
        $this->assertResponseContains('OPTIONS YES');
    }

    public function testAddPageLoadsForSpecificEntry(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entries->saveOrFail($entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Sibling Event Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'sibling@example.com',
            'entry_mobile' => '07123450000',
            'security_code' => '',
        ]));

        $events = $this->getTableLocator()->get('Events');
        /** @var \App\Model\Entity\Event $otherEvent */
        $otherEvent = $events->newEntity([
            'event_name' => 'Other Event',
            'event_description' => 'Other Event',
            'booking_code' => 'OTHER',
            'start_time' => '2026-03-17 10:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);
        $events->saveOrFail($otherEvent);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $otherCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 99,
            'checkpoint_name' => 'Other Event Checkpoint',
            'event_id' => $otherEvent->id,
        ]);
        $checkpoints->saveOrFail($otherCheckpoint);

        $entries->saveOrFail($entries->newEntity([
            'event_id' => $otherEvent->id,
            'entry_name' => 'Other Event Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'other@example.com',
            'entry_mobile' => '07123450001',
            'security_code' => '',
        ]));

        $this->get('/check-ins/add/2342ad37-13f0-4fd1-bd3f-2032273626ce');

        $this->assertResponseOk();
        $this->assertResponseContains('checkpoint-id');
        $this->assertResponseContains('participants');
        $this->assertResponseContains('Lorem ipsum dolor');
        $this->assertResponseContains('[1] Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Sibling Event Entry');
        $this->assertResponseNotContains('Other Event Checkpoint');
        $this->assertResponseNotContains('Other Event Entry');
    }

    public function testCheckpointPageLoads(): void
    {
        $this->disableErrorHandlerMiddleware();
        $this->expectException(MissingTemplateException::class);
        $this->get('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');
    }

    public function testCheckpointPostCreatesCheckIn(): void
    {
        $this->enableFormTokens();
        $this->post('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_reference' => '1',
            'participants' => ['5045fd83-55db-4d36-8a8a-63222e50e3fd'],
        ]);

        $this->assertRedirectContains('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $this->assertGreaterThan(1, $checkIns->find()->count());
    }

    public function testCheckpointPanelFragmentLoadsForNumericReference(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Available',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));

        $this->configRequest([
            'headers' => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ]);

        $this->get('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7?fragment=panel&entry_reference=1');

        $this->assertResponseOk();
        $this->assertResponseContains('checkpoint-checkin-panel');
        $this->assertResponseContains('Save Check In');
        $this->assertResponseContains('value="1"');
        $this->assertResponseContains('Available');
        $this->assertResponseNotContains('5045fd83-55db-4d36-8a8a-63222e50e3fd');
    }

    public function testCheckpointPanelFragmentLoadsForFullReference(): void
    {
        $events = $this->getTableLocator()->get('Events');
        /** @var \App\Model\Entity\Event $event */
        $event = $events->get('3a6d9419-b621-45cf-a13e-4db9647bf5bc');
        $event->booking_code = 'GW26';
        $events->saveOrFail($event);

        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Full',
            'last_name' => 'Reference',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));

        $this->configRequest([
            'headers' => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ]);

        $this->get('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7?fragment=panel&entry_reference=GW26-1');

        $this->assertResponseOk();
        $this->assertResponseContains('value="GW26-1"');
        $this->assertResponseContains('GW26-1');
        $this->assertResponseContains('Full');
    }
}
