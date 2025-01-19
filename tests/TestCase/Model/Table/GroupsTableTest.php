<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GroupsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GroupsTable Test Case
 */
class GroupsTableTest extends TestCase
{
    use TableTestTrait;

    /**
     * Test subject
     *
     * @var \App\Model\Table\GroupsTable
     */
    protected $Groups;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Groups',
        'app.Sections',
        'app.ParticipantTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Groups') ? [] : ['className' => GroupsTable::class];
        $this->Groups = $this->getTableLocator()->get('Groups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Groups);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\GroupsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $goodData = [
            'group_name' => 'Test Group',
            'visible' => true,
        ];

        $this->validatorTest($this, $this->Groups, $goodData);
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\GroupsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
