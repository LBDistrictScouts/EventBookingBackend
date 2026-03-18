<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Entity\Participant;
use App\Model\Table\ParticipantsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ParticipantsTable Test Case
 */
class ParticipantsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ParticipantsTable
     */
    protected ParticipantsTable $Participants;

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
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Participants') ? [] : ['className' => ParticipantsTable::class];
        /** @var \App\Model\Table\ParticipantsTable $participants */
        $participants = $this->getTableLocator()->get('Participants', $config);
        $this->Participants = $participants;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $participant = $this->Participants->newEntity([
            'first_name' => 'Joe',
            'last_name' => 'Bloggs',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);

        $this->assertEmpty($participant->getErrors());

        $invalid = $this->Participants->newEntity([
            'first_name' => '',
            'last_name' => '',
            'entry_id' => 'not-a-uuid',
            'participant_type_id' => '',
            'section_id' => 'not-a-uuid',
            'checked_in' => 'yes',
            'checked_out' => 'no',
            'highest_check_in_sequence' => 'top',
        ]);

        $this->assertArrayHasKey('first_name', $invalid->getErrors());
        $this->assertArrayHasKey('last_name', $invalid->getErrors());
        $this->assertArrayHasKey('entry_id', $invalid->getErrors());
        $this->assertArrayHasKey('participant_type_id', $invalid->getErrors());
        $this->assertArrayHasKey('section_id', $invalid->getErrors());
        $this->assertArrayHasKey('checked_in', $invalid->getErrors());
        $this->assertArrayHasKey('checked_out', $invalid->getErrors());
        $this->assertArrayHasKey('highest_check_in_sequence', $invalid->getErrors());
    }

    /**
     * Test counter cache behaviour method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantsTable::validationDefault()
     */
    public function testCounterCache(): void
    {
        // Entry Participant Count
        $entry = $this->Participants->Entries->find()->first();
        $startingCount = $entry->participant_count;

        // Event Participant Count
        $event = $this->Participants->Entries->Events->find()->first();
        $eventStartCount = $event->participant_count;

        $goodData = [
            'first_name' => 'Joe',
            'last_name' => 'Bloggs',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'created' => 1737039597,
            'modified' => 1737039597,
            'deleted' => null,
            'highest_check_in_sequence' => 0,
        ];
        $newParticipant = $this->Participants->newEntity($goodData);

        $this->Participants->save($newParticipant);

        // Entry Participant Count
        $entry = $this->Participants->Entries->find()->first();
        $secondCount = $entry->participant_count;

        $this->assertNotEquals($startingCount, $secondCount);
        $this->assertEquals($startingCount + 1, $secondCount);

        // Event Participant Count
        $event = $this->Participants->Entries->Events->find()->first();
        $eventSecondCount = $event->participant_count;

        $this->assertNotEquals($eventStartCount, $eventSecondCount);
        $this->assertEquals($eventStartCount + 1, $eventSecondCount);
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $invalid = $this->Participants->newEntity([
            'first_name' => 'Joe',
            'last_name' => 'Bloggs',
            'entry_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'participant_type_id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            'section_id' => 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);

        $this->assertFalse($this->Participants->save($invalid));
        $this->assertNotEmpty($invalid->getError('entry_id'));
        $this->assertNotEmpty($invalid->getError('participant_type_id'));
        $this->assertNotEmpty($invalid->getError('section_id'));
    }

    public function testFindBeforeSequenceCanFilterBySequenceEntryAndCheckpoint(): void
    {
        $participant = $this->Participants->newEntity([
            'first_name' => 'Eligible',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($participant);

        $result = $this->Participants->find(
            'beforeSequence',
            sequence: 2,
            minimumSequence: 0,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            entryId: '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            excludeCheckpointId: '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
        )
            ->all()
            ->toList();

        $this->assertCount(1, $result);
        $participant = current($result);
        $this->assertInstanceOf(Participant::class, $participant);
        $this->assertSame('Eligible', $participant->first_name);
    }

    public function testFindBeforeSequenceUsesUncheckedParticipantsForSequenceZero(): void
    {
        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 99,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $checkedIn = $this->Participants->newEntity([
            'first_name' => 'Already',
            'last_name' => 'CheckedIn',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($checkedIn);

        $result = $this->Participants->find(
            'beforeSequence',
            sequence: 0,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Unchecked', $result);
        $this->assertNotContains('Already', $result);
    }

    public function testFindBeforeSequenceUsesCheckedInNotCheckedOutParticipantsForPositiveCheckpoint(): void
    {
        $eligible = $this->Participants->newEntity([
            'first_name' => 'Eligible',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($eligible);

        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        $result = $this->Participants->find(
            'beforeSequence',
            sequence: 1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Eligible', $result);
        $this->assertNotContains('Unchecked', $result);
        $this->assertNotContains('Checked', $result);
    }

    public function testFindActiveBeforeSequenceReturnsCheckedInWalkersBeforeCheckpoint(): void
    {
        $before = $this->Participants->newEntity([
            'first_name' => 'Before',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 1,
        ]);
        $this->Participants->saveOrFail($before);

        $notCheckedIn = $this->Participants->newEntity([
            'first_name' => 'Pending',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($notCheckedIn);

        $result = $this->Participants->find(
            'activeBeforeSequence',
            sequence: 3,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Before', $result);
        $this->assertNotContains('Pending', $result);
    }

    public function testFindActiveBeforeSequenceUsesUncheckedParticipantsForStartCheckpoint(): void
    {
        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $checkedIn = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'In',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 1,
        ]);
        $this->Participants->saveOrFail($checkedIn);

        $result = $this->Participants->find(
            'activeBeforeSequence',
            sequence: 0,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Unchecked', $result);
        $this->assertNotContains('Checked', $result);
    }

    public function testFindBetweenSequencesReturnsTransitParticipants(): void
    {
        $between = $this->Participants->newEntity([
            'first_name' => 'Between',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $this->Participants->saveOrFail($between);

        $tooEarly = $this->Participants->newEntity([
            'first_name' => 'TooEarly',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 1,
        ]);
        $this->Participants->saveOrFail($tooEarly);

        $reached = $this->Participants->newEntity([
            'first_name' => 'Reached',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 3,
        ]);
        $this->Participants->saveOrFail($reached);

        $result = $this->Participants->find(
            'betweenSequences',
            sequence: 3,
            minimumSequence: 2,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Between', $result);
        $this->assertNotContains('TooEarly', $result);
        $this->assertNotContains('Reached', $result);
    }

    public function testPositiveCheckpointFindersHandleCumulativeBeforeAndWindowedBetween(): void
    {
        $checkpointOne = $this->Participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'One',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 1,
        ]);
        $this->Participants->saveOrFail($checkpointOne);

        $checkpointTwo = $this->Participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Two',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $this->Participants->saveOrFail($checkpointTwo);

        $checkpointThree = $this->Participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Three',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 3,
        ]);
        $this->Participants->saveOrFail($checkpointThree);

        $checkpointFour = $this->Participants->newEntity([
            'first_name' => 'Checkpoint',
            'last_name' => 'Four',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 4,
        ]);
        $this->Participants->saveOrFail($checkpointFour);

        $before = $this->Participants->find(
            'activeBeforeSequence',
            sequence: 4,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $between = $this->Participants->find(
            'betweenSequences',
            sequence: 4,
            minimumSequence: 3,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $this->assertContains('One', $before);
        $this->assertContains('Two', $before);
        $this->assertContains('Three', $before);
        $this->assertNotContains('Four', $before);

        $this->assertContains('Three', $between);
        $this->assertNotContains('One', $between);
        $this->assertNotContains('Two', $between);
        $this->assertNotContains('Four', $between);
    }

    public function testFindBetweenSequencesForFirstPositiveCheckpointUsesCheckedInWalkers(): void
    {
        $startCheckedIn = $this->Participants->newEntity([
            'first_name' => 'Started',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($startCheckedIn);

        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $result = $this->Participants->find(
            'betweenSequences',
            sequence: 1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Started', $result);
        $this->assertNotContains('Unchecked', $result);
    }

    public function testFindBetweenSequencesForStartUsesUncheckedParticipants(): void
    {
        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $checkedIn = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'In',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($checkedIn);

        $result = $this->Participants->find(
            'betweenSequences',
            sequence: 0,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Unchecked', $result);
        $this->assertNotContains('Checked', $result);
    }

    public function testFindReachedSequenceReturnsParticipantsAtOrBeyondCheckpoint(): void
    {
        $reached = $this->Participants->newEntity([
            'first_name' => 'Reached',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 3,
        ]);
        $this->Participants->saveOrFail($reached);

        $before = $this->Participants->newEntity([
            'first_name' => 'Before',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $this->Participants->saveOrFail($before);

        $result = $this->Participants->find(
            'reachedSequence',
            sequence: 3,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Reached', $result);
        $this->assertNotContains('Before', $result);
    }

    public function testFindReachedSequenceForPositiveCheckpointIncludesCheckedOutFinishers(): void
    {
        $finisher = $this->Participants->newEntity([
            'first_name' => 'Finished',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($finisher);

        $result = $this->Participants->find(
            'reachedSequence',
            sequence: 1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Finished', $result);
    }

    public function testFindActiveBeforeSequenceExcludesFinishAndSurveyForPositiveCheckpoint(): void
    {
        $before = $this->Participants->newEntity([
            'first_name' => 'Before',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 2,
        ]);
        $this->Participants->saveOrFail($before);

        $finish = $this->Participants->newEntity([
            'first_name' => 'Finished',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($finish);

        $survey = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -2,
        ]);
        $this->Participants->saveOrFail($survey);

        $result = $this->Participants->find(
            'activeBeforeSequence',
            sequence: 3,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Before', $result);
        $this->assertNotContains('Finished', $result);
        $this->assertNotContains('Survey', $result);
    }

    public function testFindReachedSequenceTreatsFinishAndSurveyAsBeyondPositiveCheckpoint(): void
    {
        $finish = $this->Participants->newEntity([
            'first_name' => 'Finished',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($finish);

        $survey = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -2,
        ]);
        $this->Participants->saveOrFail($survey);

        $result = $this->Participants->find(
            'reachedSequence',
            sequence: 3,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Finished', $result);
        $this->assertContains('Survey', $result);
    }

    public function testFindReachedSequenceForSurveyUsesDirectParticipantCheckIn(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        /** @var \App\Model\Entity\Checkpoint $surveyCheckpoint */
        $surveyCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -2,
            'checkpoint_name' => 'Survey Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($surveyCheckpoint);

        /** @var \App\Model\Entity\Participant $done */
        $done = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Done',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($done);

        $pending = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Pending',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($pending);

        $checkIns = $this->getTableLocator()->get('CheckIns');
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        $surveyCheckIn = $checkIns->newEntity([
            'checkpoint_id' => $surveyCheckpoint->id,
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
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

        $result = $this->Participants->find(
            'reachedSequence',
            sequence: -2,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $this->assertContains('Done', $result);
        $this->assertNotContains('Pending', $result);
        $this->assertSame(
            1,
            $this->Participants->find(
                'reachedSequence',
                sequence: -2,
                eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            )->count(),
        );
    }

    public function testFindBetweenSequencesForFinishIncludesAllActiveWalkers(): void
    {
        $walkingLow = $this->Participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'Low',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($walkingLow);

        $walkingHigh = $this->Participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'High',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 5,
        ]);
        $this->Participants->saveOrFail($walkingHigh);

        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 5,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        $result = $this->Participants->find(
            'betweenSequences',
            sequence: -1,
            minimumSequence: 5,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $this->assertContains('Low', $result);
        $this->assertContains('High', $result);
        $this->assertNotContains('Out', $result);
    }

    public function testFindReachedSequenceForFinishUsesCheckedOutParticipants(): void
    {
        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        $walking = $this->Participants->newEntity([
            'first_name' => 'Still',
            'last_name' => 'Walking',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($walking);

        $result = $this->Participants->find(
            'reachedSequence',
            sequence: -1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $this->assertContains('Out', $result);
        $this->assertNotContains('Walking', $result);
    }

    public function testFindBetweenSequencesHandlesFinishToSurveyWindow(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        /** @var \App\Model\Entity\Checkpoint $surveyCheckpoint */
        $surveyCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -2,
            'checkpoint_name' => 'Survey Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($surveyCheckpoint);

        $finish = $this->Participants->newEntity([
            'first_name' => 'Finished',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($finish);

        /** @var \App\Model\Entity\Participant $survey */
        $survey = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($survey);

        $checkIns = $this->getTableLocator()->get('CheckIns');
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        $surveyCheckIn = $checkIns->newEntity([
            'checkpoint_id' => $surveyCheckpoint->id,
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2026-03-17 12:00:00',
            'participant_count' => 1,
        ]);
        $checkIns->saveOrFail($surveyCheckIn);

        $participantsCheckIns = $this->getTableLocator()->get('ParticipantsCheckIns');
        $participantsCheckIns->getConnection()->insert('participants_check_ins', [
            'check_in_id' => $surveyCheckIn->id,
            'participant_id' => $survey->id,
            'created' => '2026-03-17 12:00:00',
            'modified' => '2026-03-17 12:00:00',
            'deleted' => null,
        ]);

        $normal = $this->Participants->newEntity([
            'first_name' => 'Normal',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 4,
        ]);
        $this->Participants->saveOrFail($normal);

        $result = $this->Participants->find(
            'betweenSequences',
            sequence: -2,
            minimumSequence: -1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Finished', $result);
        $this->assertNotContains('Survey', $result);
        $this->assertContains('Normal', $result);
    }

    public function testFindBeforeSequenceForSurveyIncludesCheckedOutParticipants(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        /** @var \App\Model\Entity\Checkpoint $surveyCheckpoint */
        $surveyCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -2,
            'checkpoint_name' => 'Survey Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($surveyCheckpoint);

        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        /** @var \App\Model\Entity\Participant $surveyDone */
        $surveyDone = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Done',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($surveyDone);

        $checkIns = $this->getTableLocator()->get('CheckIns');
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        $surveyCheckIn = $checkIns->newEntity([
            'checkpoint_id' => $surveyCheckpoint->id,
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2026-03-17 12:00:00',
            'participant_count' => 1,
        ]);
        $checkIns->saveOrFail($surveyCheckIn);

        $participantsCheckIns = $this->getTableLocator()->get('ParticipantsCheckIns');
        $participantsCheckIns->getConnection()->insert('participants_check_ins', [
            'check_in_id' => $surveyCheckIn->id,
            'participant_id' => $surveyDone->id,
            'created' => '2026-03-17 12:00:00',
            'modified' => '2026-03-17 12:00:00',
            'deleted' => null,
        ]);

        $result = $this->Participants->find(
            'beforeSequence',
            sequence: -2,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Checked', $result);
        $this->assertNotContains('Survey', $result);
    }

    public function testFindBeforeSequenceForSurveyExcludesDirectParticipantCheckIn(): void
    {
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        /** @var \App\Model\Entity\Checkpoint $surveyCheckpoint */
        $surveyCheckpoint = $checkpoints->newEntity([
            'checkpoint_sequence' => -2,
            'checkpoint_name' => 'Survey Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);
        $checkpoints->saveOrFail($surveyCheckpoint);

        /** @var \App\Model\Entity\Participant $done */
        $done = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Done',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($done);

        $pending = $this->Participants->newEntity([
            'first_name' => 'Survey',
            'last_name' => 'Pending',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => -1,
        ]);
        $this->Participants->saveOrFail($pending);

        $checkIns = $this->getTableLocator()->get('CheckIns');
        /** @var \App\Model\Entity\CheckIn $surveyCheckIn */
        $surveyCheckIn = $checkIns->newEntity([
            'checkpoint_id' => $surveyCheckpoint->id,
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
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

        $result = $this->Participants->find(
            'beforeSequence',
            sequence: -2,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('last_name')
            ->toList();

        $this->assertContains('Pending', $result);
        $this->assertNotContains('Done', $result);
    }

    public function testFindStillWalkingReturnsOnlyUncheckedOutParticipants(): void
    {
        $walking = $this->Participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 4,
        ]);
        $this->Participants->saveOrFail($walking);

        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Stopped',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 4,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        $result = $this->Participants->find(
            'stillWalking',
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Walking', $result);
        $this->assertNotContains('Stopped', $result);
    }

    public function testFindStillWalkingForPositiveCheckpointExcludesUncheckedParticipants(): void
    {
        $walking = $this->Participants->newEntity([
            'first_name' => 'Walking',
            'last_name' => 'Started',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($walking);

        $unchecked = $this->Participants->newEntity([
            'first_name' => 'Unchecked',
            'last_name' => 'Walker',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $this->Participants->saveOrFail($unchecked);

        $result = $this->Participants->find(
            'stillWalking',
            sequence: 1,
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Walking', $result);
        $this->assertNotContains('Unchecked', $result);
    }

    public function testFindCheckedOutReturnsOnlyCheckedOutParticipants(): void
    {
        $checkedOut = $this->Participants->newEntity([
            'first_name' => 'Checked',
            'last_name' => 'Out',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => true,
            'highest_check_in_sequence' => 5,
        ]);
        $this->Participants->saveOrFail($checkedOut);

        $stillWalking = $this->Participants->newEntity([
            'first_name' => 'Still',
            'last_name' => 'Walking',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => true,
            'checked_out' => false,
            'highest_check_in_sequence' => 5,
        ]);
        $this->Participants->saveOrFail($stillWalking);

        $result = $this->Participants->find(
            'checkedOut',
            eventId: '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        )
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertContains('Checked', $result);
        $this->assertNotContains('Still', $result);
    }
}
