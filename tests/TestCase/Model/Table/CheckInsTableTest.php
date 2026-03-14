<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CheckInsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CheckInsTable Test Case
 */
class CheckInsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CheckInsTable
     */
    protected $CheckIns;

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

        'app.CheckIns',
        'app.ParticipantsCheckIns',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CheckIns') ? [] : ['className' => CheckInsTable::class];
        $this->CheckIns = $this->getTableLocator()->get('CheckIns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CheckIns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CheckInsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $checkIn = $this->CheckIns->newEntity([
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2025-01-16 10:00:00',
            'participant_count' => 0,
        ]);

        $this->assertEmpty($checkIn->getErrors());

        $invalid = $this->CheckIns->newEntity([
            'checkpoint_id' => 'not-a-uuid',
            'entry_id' => '',
            'check_in_time' => '',
            'participant_count' => 'nope',
        ]);

        $this->assertArrayHasKey('checkpoint_id', $invalid->getErrors());
        $this->assertArrayHasKey('entry_id', $invalid->getErrors());
        $this->assertArrayHasKey('check_in_time', $invalid->getErrors());
        $this->assertArrayHasKey('participant_count', $invalid->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CheckInsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $invalid = $this->CheckIns->newEntity([
            'checkpoint_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'entry_id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            'check_in_time' => '2025-01-16 10:00:00',
            'participant_count' => 0,
        ]);

        $this->assertFalse($this->CheckIns->save($invalid));
        $this->assertNotEmpty($invalid->getError('checkpoint_id'));
        $this->assertNotEmpty($invalid->getError('entry_id'));
    }
}
