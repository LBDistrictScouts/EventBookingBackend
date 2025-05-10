<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

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
    protected $Participants;

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
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Participants') ? [] : ['className' => ParticipantsTable::class];
        $this->Participants = $this->getTableLocator()->get('Participants', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Participants);

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
        $this->markTestIncomplete('Not implemented yet.');
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
        $this->markTestIncomplete('Not implemented yet.');
    }
}
