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
        'app.Groups',
        'app.ParticipantTypes',
        'app.Sections',
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
            'notification_email' => 'section@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 29,
        ];

        $validation = [
            'require' => ['section_name'],
            'notEmpty' => ['section_name', 'participant_type_id', 'group_id'],
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
        $duplicate = $this->Sections->newEntity([
            'section_name' => 'Different Name',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 1,
        ]);

        $this->assertFalse($this->Sections->save($duplicate));
        $this->assertNotEmpty($duplicate->getError('osm_section_id'));

        $invalid = $this->Sections->newEntity([
            'section_name' => 'Missing Links',
            'participant_type_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'group_id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            'osm_section_id' => 99,
        ]);

        $this->assertFalse($this->Sections->save($invalid));
        $this->assertNotEmpty($invalid->getError('participant_type_id'));
        $this->assertNotEmpty($invalid->getError('group_id'));
    }
}
