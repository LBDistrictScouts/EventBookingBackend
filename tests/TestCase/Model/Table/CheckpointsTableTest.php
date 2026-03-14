<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CheckpointsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CheckpointsTable Test Case
 */
class CheckpointsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CheckpointsTable
     */
    protected $Checkpoints;

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
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Checkpoints') ? [] : ['className' => CheckpointsTable::class];
        $this->Checkpoints = $this->getTableLocator()->get('Checkpoints', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Checkpoints);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CheckpointsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $checkpoint = $this->Checkpoints->newEntity([
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Start',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertEmpty($checkpoint->getErrors());

        $invalid = $this->Checkpoints->newEntity([
            'checkpoint_sequence' => 'invalid',
            'checkpoint_name' => '',
            'event_id' => 'not-a-uuid',
        ]);

        $this->assertArrayHasKey('checkpoint_sequence', $invalid->getErrors());
        $this->assertArrayHasKey('checkpoint_name', $invalid->getErrors());
        $this->assertArrayHasKey('event_id', $invalid->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CheckpointsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $duplicate = $this->Checkpoints->newEntity([
            'checkpoint_sequence' => 1,
            'checkpoint_name' => 'Duplicate',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertFalse($this->Checkpoints->save($duplicate));
        $this->assertNotEmpty($duplicate->getError('checkpoint_sequence'));

        $invalidEvent = $this->Checkpoints->newEntity([
            'checkpoint_sequence' => 99,
            'checkpoint_name' => 'Missing Event',
            'event_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
        ]);

        $this->assertFalse($this->Checkpoints->save($invalidEvent));
        $this->assertNotEmpty($invalidEvent->getError('event_id'));
    }
}
