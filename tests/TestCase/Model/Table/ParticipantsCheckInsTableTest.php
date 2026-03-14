<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ParticipantsCheckInsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ParticipantsCheckInsTable Test Case
 */
class ParticipantsCheckInsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ParticipantsCheckInsTable
     */
    protected $ParticipantsCheckIns;

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
        $config = $this->getTableLocator()->exists('ParticipantsCheckIns') ? [] : ['className' => ParticipantsCheckInsTable::class];
        $this->ParticipantsCheckIns = $this->getTableLocator()->get('ParticipantsCheckIns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ParticipantsCheckIns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantsCheckInsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $validator = $this->ParticipantsCheckIns->getValidator('default');

        $this->assertArrayHasKey('id', $validator->validate([]));
        $this->assertArrayHasKey('id', $validator->validate(['id' => 'not-a-uuid']));
        $this->assertSame([], $validator->validate(['id' => '11111111-1111-4111-8111-111111111111']));
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ParticipantsCheckInsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $invalid = $this->ParticipantsCheckIns->newEmptyEntity();
        $invalid->set('id', '11111111-1111-4111-8111-111111111111');
        $invalid->set('check_in_id', 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa');
        $invalid->set('participant_id', 'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb');

        $this->assertFalse($this->ParticipantsCheckIns->save($invalid));
        $this->assertNotEmpty($invalid->getError('check_in_id'));
        $this->assertNotEmpty($invalid->getError('participant_id'));
    }
}
