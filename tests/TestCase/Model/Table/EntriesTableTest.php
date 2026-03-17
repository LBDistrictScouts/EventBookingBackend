<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EntriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EntriesTable Test Case
 */
class EntriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EntriesTable
     */
    protected $Entries;

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
        $config = $this->getTableLocator()->exists('Entries') ? [] : ['className' => EntriesTable::class];
        $this->Entries = $this->getTableLocator()->get('Entries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Entries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\EntriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $entry = $this->Entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Valid Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'valid@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);

        $this->assertEmpty($entry->getErrors());

        $invalid = $this->Entries->newEntity([
            'event_id' => 'not-a-uuid',
            'entry_name' => '',
            'active' => 'yes',
            'participant_count' => 'one',
            'checked_in_count' => 'one',
            'entry_email' => 'not-an-email',
            'entry_mobile' => str_repeat('1', 21),
            'security_code' => '123456',
        ]);

        $this->assertArrayHasKey('event_id', $invalid->getErrors());
        $this->assertArrayHasKey('entry_name', $invalid->getErrors());
        $this->assertArrayHasKey('active', $invalid->getErrors());
        $this->assertArrayHasKey('participant_count', $invalid->getErrors());
        $this->assertArrayHasKey('checked_in_count', $invalid->getErrors());
        $this->assertArrayHasKey('entry_email', $invalid->getErrors());
        $this->assertArrayHasKey('entry_mobile', $invalid->getErrors());
        $this->assertArrayHasKey('security_code', $invalid->getErrors());
    }

    public function testNewEntityGeneratesSecurityCodeWhenFieldIsMissing(): void
    {
        $entry = $this->Entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Generated Security Code',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'generated@example.com',
            'entry_mobile' => '07123456789',
        ]);

        $this->assertEmpty($entry->getErrors());
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{5}$/', (string)$entry->security_code);
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\EntriesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $duplicate = $this->Entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Lorem ipsum dolor sit amet',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'duplicate@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);

        $this->assertFalse($this->Entries->save($duplicate));
        $this->assertNotEmpty($duplicate->getError('entry_name'));

        $invalidEvent = $this->Entries->newEntity([
            'event_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'entry_name' => 'Missing Event',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'missing@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);

        $this->assertFalse($this->Entries->save($invalidEvent));
        $this->assertNotEmpty($invalidEvent->getError('event_id'));
    }

    public function testMerge(): void
    {
        $entryTemplate = $this->Entries->find()->firstOrFail();
        $participantTemplate = $this->Entries->Participants->find()->firstOrFail();

        $entryOne = $this->Entries->patchEntity($this->Entries->newEmptyEntity(), [
            'event_id' => $entryTemplate->event_id,
            'entry_name' => 'Survivor',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'survivor@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);
        $entryOne = $this->Entries->save($entryOne);

        $entryTwo = $this->Entries->patchEntity($this->Entries->newEmptyEntity(), [
            'event_id' => $entryTemplate->event_id,
            'entry_name' => 'Victim',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'victim@example.com',
            'entry_mobile' => '07987654321',
            'security_code' => '',
        ]);
        $entryTwo = $this->Entries->save($entryTwo);

        $this->assertNotFalse($entryOne);
        $this->assertNotFalse($entryTwo);

        $this->assertIsString($entryOne->id);
        $this->assertIsString($entryTwo->id);
        $this->assertNotEquals($entryOne->id, $entryTwo->id);

        $participantOne = $this->Entries->Participants->patchEntity($this->Entries->Participants->newEmptyEntity(), [
            'first_name' => $participantTemplate->first_name,
            'last_name' => $participantTemplate->last_name,
            'entry_id' => $entryOne->id,
            'participant_type_id' => $participantTemplate->participant_type_id,
            'section_id' => $participantTemplate->section_id,
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $participantTwo = $this->Entries->Participants->patchEntity($this->Entries->Participants->newEmptyEntity(), [
            'first_name' => 'Test',
            'last_name' => $participantTemplate->last_name,
            'entry_id' => $entryTwo->id,
            'participant_type_id' => $participantTemplate->participant_type_id,
            'section_id' => $participantTemplate->section_id,
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);

        $this->assertNotFalse($this->Entries->Participants->save($participantOne));
        $this->assertNotFalse($this->Entries->Participants->save($participantTwo));
        $this->assertSame(1, $this->Entries->Participants->find()->where(['entry_id' => $entryOne->id])->count());
        $this->assertSame(1, $this->Entries->Participants->find()->where(['entry_id' => $entryTwo->id])->count());

        $result = $this->Entries->mergeEntries($entryOne->id, $entryTwo->id);
        $this->assertSame(2, $result);

        $entryOne = $this->Entries->get($entryOne->id, contain: ['Participants']);
        $this->assertSame(2, $entryOne->participant_count);
        $this->assertSame(2, $this->Entries->Participants->find()->where(['entry_id' => $entryOne->id])->count());
    }
}
