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
        'app.Groups',
        'app.ParticipantTypes',
        'app.Sections',

        'app.Events',
        'app.EventsSections',
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
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\EventsSectionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $link = $this->EventsSections->newEntity([
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertEmpty($link->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\EventsSectionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $invalid = $this->EventsSections->newEmptyEntity();
        $invalid->set('section_id', 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa');
        $invalid->set('event_id', 'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb');

        $this->assertFalse($this->EventsSections->save($invalid));
        $this->assertNotEmpty($invalid->getError('section_id'));
        $this->assertNotEmpty($invalid->getError('event_id'));
    }
}
