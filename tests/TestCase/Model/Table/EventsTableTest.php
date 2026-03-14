<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EventsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EventsTable Test Case
 */
class EventsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EventsTable
     */
    protected $Events;

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
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Events') ? [] : ['className' => EventsTable::class];
        $this->Events = $this->getTableLocator()->get('Events', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Events);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\EventsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $event = $this->Events->newEntity([
            'event_name' => 'Winter Hike',
            'event_description' => 'A weekend hike.',
            'booking_code' => 'WINTER2026',
            'start_time' => '2026-01-10 09:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);

        $this->assertEmpty($event->getErrors());

        $invalid = $this->Events->newEntity([
            'event_name' => '',
            'event_description' => '',
            'booking_code' => '',
            'start_time' => '',
            'bookable' => 'yes',
            'finished' => 'no',
            'entry_count' => 'many',
            'participant_count' => 'many',
            'checked_in_count' => 'many',
        ]);

        $this->assertArrayHasKey('event_name', $invalid->getErrors());
        $this->assertArrayHasKey('event_description', $invalid->getErrors());
        $this->assertArrayHasKey('booking_code', $invalid->getErrors());
        $this->assertArrayHasKey('start_time', $invalid->getErrors());
        $this->assertArrayHasKey('bookable', $invalid->getErrors());
        $this->assertArrayHasKey('finished', $invalid->getErrors());
        $this->assertArrayHasKey('entry_count', $invalid->getErrors());
        $this->assertArrayHasKey('participant_count', $invalid->getErrors());
        $this->assertArrayHasKey('checked_in_count', $invalid->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\EventsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $duplicate = $this->Events->newEntity([
            'event_name' => 'Lorem ipsum dolor sit amet',
            'event_description' => 'Duplicate',
            'booking_code' => 'NEWCODE123',
            'start_time' => '2026-01-10 09:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);

        $this->assertFalse($this->Events->save($duplicate));
        $this->assertNotEmpty($duplicate->getError('event_name'));

        $duplicateCode = $this->Events->newEntity([
            'event_name' => 'Different Event',
            'event_description' => 'Duplicate code',
            'booking_code' => 'Lorem ipsum dolor ',
            'start_time' => '2026-02-10 09:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
        ]);

        $this->assertFalse($this->Events->save($duplicateCode));
        $this->assertNotEmpty($duplicateCode->getError('booking_code'));
    }
}
