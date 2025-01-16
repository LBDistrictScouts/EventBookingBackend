<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EventsSectionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EventsSectionsTable Test Case
 */
class EventsSectionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EventsSectionsTable
     */
    protected $EventsSections;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.EventsSections',
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
        $config = $this->getTableLocator()->exists('EventsSections') ? [] : ['className' => EventsSectionsTable::class];
        $this->EventsSections = $this->getTableLocator()->get('EventsSections', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->EventsSections);

        parent::tearDown();
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\EventsSectionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
