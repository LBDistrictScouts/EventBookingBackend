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
        'app.Checkpoints',
        'app.Events',
        'app.CheckIns',
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CheckpointsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
