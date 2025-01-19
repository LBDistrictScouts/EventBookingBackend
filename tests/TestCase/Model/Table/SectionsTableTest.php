<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SectionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SectionsTable Test Case
 */
class SectionsTableTest extends TestCase
{
    use TableTestTrait;

    /**
     * Test subject
     *
     * @var \App\Model\Table\SectionsTable
     */
    protected $Sections;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Sections',
        'app.ParticipantTypes',
        'app.Groups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Sections') ? [] : ['className' => SectionsTable::class];
        $this->Sections = $this->getTableLocator()->get('Sections', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Sections);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SectionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $goodData = [
            'id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'section_name' => 'Lorem ipsum dolor sit amet',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 1,
        ];

        $validation = [
            'require' => ['section_name'],
            'notEmpty' => ['section_name', 'participant_type_id', 'group_id', 'osm_section_id'],
        ];

        $this->validatorTest($this, $this->Sections, $goodData, $validation);
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SectionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
