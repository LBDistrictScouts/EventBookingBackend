<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CheckpointsController Test Case
 *
 * @uses \App\Controller\CheckpointsController
 */
class CheckpointsControllerTest extends TestCase
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
     * @uses \App\Controller\CheckpointsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/checkpoints/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('checkpoints', $data);
        $this->assertCount(1, $data['checkpoints']);
    }

    public function testIndexDefaultsToCurrentEventButCanShowAll(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $otherEvent = $events->newEntity([
            'event_name' => 'Other Event',
            'event_description' => 'Other Event',
            'booking_code' => 'OTHER',
            'start_time' => '2026-03-18 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);
        $events->saveOrFail($otherEvent);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $otherCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 9,
            'checkpoint_name' => 'Other Event Checkpoint',
            'event_id' => $otherEvent->id,
        ]);
        $checkpoints->saveOrFail($otherCheckpoint);

        $this->get('/checkpoints');
        $this->assertResponseOk();
        $this->assertResponseContains('Show All Checkpoints');
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseNotContains('Other Event Checkpoint');

        $this->get('/checkpoints?all=1');
        $this->assertResponseOk();
        $this->assertResponseContains('Show Current Event Only');
        $this->assertResponseContains('Other Event Checkpoint');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::view()
     */
    public function testView(): void
    {
        $this->get('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('checkpoint', $data);
        $this->assertSame('Lorem ipsum dolor sit amet', $data['checkpoint']['checkpoint_name']);
    }

    public function testViewShowsParticipantsBetweenPreviousAndCurrentCheckpoint(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $previousCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Checkpoint Two',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($previousCheckpoint);

        $currentCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 3,
            'checkpoint_name' => 'Checkpoint Three',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($currentCheckpoint);

        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->newEntity([
            'first_name' => 'Transit',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $participants->saveOrFail($participant);

        $this->get('/checkpoints/view/' . $currentCheckpoint->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Before Count');
        $this->assertResponseContains('Between Count');
        $this->assertResponseContains('Checked In Here Count');
        $this->assertResponseContains('Total Still Walking');
        $this->assertResponseContains('Total Checked Out');
        $this->assertResponseContains('Checkpoint Two');
        $this->assertResponseContains('Transit  Walker');
        $this->assertResponseContains('Participants In Transit');
    }

    public function testViewUsesCheckedInWalkersForFirstPositiveCheckpoint(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Started',
            'last_name' => 'One',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Two',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));

        $this->get('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');

        $this->assertResponseOk();
        $this->assertResponseContains('Before Count');
        $this->assertResponseContains('Between Count');
        $this->assertResponseContains('>1<');
        $this->assertResponseContains('Participants In Transit');
    }

    public function testViewShowsUncheckedParticipantsForStartCheckpoint(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $startCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 0,
            'checkpoint_name' => 'Start Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($startCheckpoint);

        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'In',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));

        $this->get('/checkpoints/view/' . $startCheckpoint->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Start Checkpoint');
        $this->assertResponseContains('Unchecked  Walker');
        $this->assertResponseNotContains('Checked  In');
        $this->assertResponseContains('>1<');
    }

    public function testViewIgnoresNegativePreviousCheckpointsForStart(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $finishCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -1,
            'checkpoint_name' => 'Finish Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($finishCheckpoint);

        $startCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 0,
            'checkpoint_name' => 'Start Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($startCheckpoint);

        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]));

        $this->get('/checkpoints/view/' . $startCheckpoint->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Unchecked  Walker');
        $this->assertResponseContains('First checkpoint in event');
        $this->assertResponseContains('>1<');
    }

    public function testViewShowsSurveyCountsUsingDirectParticipantCheckIns(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Survey Event',
            'event_description' => 'Survey Event',
            'booking_code' => 'SURVEY',
            'start_time' => '2026-03-18 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 3,
            'checked_in_count' => 3,
        ]);
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $event->id,
            'entry_name' => 'Survey Entry',
            'reference_number' => 42,
            'active' => true,
            'participant_count' => 3,
            'checked_in_count' => 3,
            'entry_email' => 'survey@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $entries->saveOrFail($entry);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $surveyCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -2,
            'checkpoint_name' => 'Survey Checkpoint',
            'event_id' => $event->id,
        ]);
        $checkpoints->saveOrFail($surveyCheckpoint);

        $participants = $this->getTableLocator()->get('Participants');
        $done = $participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Done',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $participants->saveOrFail($done);

        $pendingCheckedOut = $participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Pending Out',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $participants->saveOrFail($pendingCheckedOut);

        $pendingWalking = $participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Pending Walking',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 3,
        ]);
        $participants->saveOrFail($pendingWalking);

        $checkIns = $this->getTableLocator()->get('CheckIns');
        $surveyCheckIn = $checkIns->newEntity([
            'checkpoint_id' => $surveyCheckpoint->id,
            'entry_id' => $entry->id,
            'check_in_time' => '2026-03-17 12:00:00',
            'participant_count' => 1,
        ]);
        $checkIns->saveOrFail($surveyCheckIn);

        $participantsCheckIns = $this->getTableLocator()->get('ParticipantsCheckIns');
        $participantsCheckIns->getConnection()->insert('participants_check_ins', [
            'check_in_id' => $surveyCheckIn->id,
            'participant_id' => $done->id,
            'created' => '2026-03-17 12:00:00',
            'modified' => '2026-03-17 12:00:00',
            'deleted' => null,
        ]);

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $surveyCheckpoint->id . '?fragment=count');

        $this->assertResponseOk();
        $this->assertResponseContains('Checked In Here Count');
        $this->assertResponseContains('>1<');
        $this->assertResponseContains('>2<');

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $surveyCheckpoint->id . '?fragment=table');

        $this->assertResponseOk();
        $this->assertResponseContains('Survey  Pending Out');
        $this->assertResponseContains('Survey  Pending Walking');
        $this->assertResponseNotContains('Survey  Done');
    }

    public function testViewShowsFinishCountsUsingCheckedOutParticipants(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Finish Event',
            'event_description' => 'Finish Event',
            'booking_code' => 'FINISH',
            'start_time' => '2026-03-18 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 3,
            'checked_in_count' => 3,
        ]);
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $event->id,
            'entry_name' => 'Finish Entry',
            'reference_number' => 99,
            'active' => true,
            'participant_count' => 3,
            'checked_in_count' => 3,
            'entry_email' => 'finish@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $entries->saveOrFail($entry);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $previousCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => 5,
            'checkpoint_name' => 'Last Normal Checkpoint',
            'event_id' => $event->id,
        ]);
        $checkpoints->saveOrFail($previousCheckpoint);

        $finishCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -1,
            'checkpoint_name' => 'Finish Checkpoint',
            'event_id' => $event->id,
        ]);
        $checkpoints->saveOrFail($finishCheckpoint);

        $participants = $this->getTableLocator()->get('Participants');
        $walkingLow = $participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'Low',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $participants->saveOrFail($walkingLow);

        $walkingHigh = $participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'High',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 5,
        ]);
        $participants->saveOrFail($walkingHigh);

        $checkedOut = $participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 0,
        ]);
        $participants->saveOrFail($checkedOut);

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $finishCheckpoint->id . '?fragment=count');

        $this->assertResponseOk();
        $this->assertResponseContains('Before Count');
        $this->assertResponseContains('Checked In Here Count');
        $this->assertResponseContains('>2<');
        $this->assertResponseContains('>1<');

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $finishCheckpoint->id . '?fragment=table');

        $this->assertResponseOk();
        $this->assertResponseContains('Walking  Low');
        $this->assertResponseContains('Walking  High');
        $this->assertResponseNotContains('Checked  Out');
    }

    public function testViewShowsPositiveCheckpointBeforeAndBetweenCountsForTransitWindow(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Positive Event',
            'event_description' => 'Positive Event',
            'booking_code' => 'POS',
            'start_time' => '2026-03-18 10:00:00',
            'bookable' => false,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 4,
            'checked_in_count' => 4,
        ]);
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $event->id,
            'entry_name' => 'Positive Entry',
            'reference_number' => 12,
            'active' => true,
            'participant_count' => 4,
            'checked_in_count' => 4,
            'entry_email' => 'positive@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $entries->saveOrFail($entry);

        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $checkpointThree = $checkpoints->newEntity([
            'checkpoint_sequence' => 3,
            'checkpoint_name' => 'Checkpoint Three',
            'event_id' => $event->id,
        ]);
        $checkpoints->saveOrFail($checkpointThree);

        $checkpointFour = $checkpoints->newEntity([
            'checkpoint_sequence' => 4,
            'checkpoint_name' => 'Checkpoint Four',
            'event_id' => $event->id,
        ]);
        $checkpoints->saveOrFail($checkpointFour);

        $participants = $this->getTableLocator()->get('Participants');
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'One',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 1,
        ]));
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Two',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]));
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Three',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 3,
        ]));
        $participants->saveOrFail($participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Four',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 4,
        ]));

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $checkpointFour->id . '?fragment=count');

        $this->assertResponseOk();
        $this->assertResponseContains('Before Count');
        $this->assertResponseContains('Between Count');
        $this->assertResponseContains('>3<');
        $this->assertResponseContains('>1<');

        $this->configRequest([
            'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
        ]);
        $this->get('/checkpoints/view/' . $checkpointFour->id . '?fragment=table');

        $this->assertResponseOk();
        $this->assertResponseContains('Checkpoint  Three');
        $this->assertResponseNotContains('Checkpoint  One');
        $this->assertResponseNotContains('Checkpoint  Two');
        $this->assertResponseNotContains('Checkpoint  Four');
    }

    public function testViewCanRenderDashboardFragmentsForAjaxRefresh(): void
    {
        $this->configRequest([
            'headers' => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ]);

        $this->get('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7?fragment=count');
        $this->assertResponseOk();
        $this->assertResponseContains('checkpoint-between-count');
        $this->assertResponseContains('Checked In Here Count');
        $this->assertResponseContains('Total Checked Out');
        $this->assertResponseNotContains('<html');

        $this->configRequest([
            'headers' => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ]);

        $this->get('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7?fragment=table');
        $this->assertResponseOk();
        $this->assertResponseContains('checkpoint-between-table');
        $this->assertResponseNotContains('<html');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/checkpoints/add', [
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Finish',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $this->assertSame(1, $checkpoints->find()->where(['checkpoint_name' => 'Finish'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/checkpoints/edit/8454694e-a2f3-4775-b75d-1fd3e57cc4b7', [
            'checkpoint_sequence' => 1,
            'checkpoint_name' => 'Updated Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $this->assertSame('Updated Checkpoint', $checkpoints->get('8454694e-a2f3-4775-b75d-1fd3e57cc4b7')->checkpoint_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/checkpoints/delete/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $deleted = $checkpoints->find('withTrashed')->where(['id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
