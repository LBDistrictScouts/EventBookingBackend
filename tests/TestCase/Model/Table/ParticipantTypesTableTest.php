<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ParticipantTypesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ParticipantTypesTable Test Case
 */
class ParticipantTypesTableTest extends TestCase
{
    use TableTestTrait;

    /**
     * Test subject
     *
     * @var \App\Model\Table\ParticipantTypesTable
     */
    protected $ParticipantTypes;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
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
        $config = $this->getTableLocator()->exists('ParticipantTypes') ? [] : ['className' => ParticipantTypesTable::class];
        $this->ParticipantTypes = $this->getTableLocator()->get('ParticipantTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ParticipantTypes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantTypesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $goodData = [
            'participant_type' => 'Test Participant Type',
            'adult' => true,
            'uniformed' => true,
            'out_of_district' => true,
        ];

        $this->validatorTest($this, $this->ParticipantTypes, $goodData);
    }
}
