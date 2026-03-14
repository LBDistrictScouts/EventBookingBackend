<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\EntriesController Test Case
 *
 * @uses \App\Controller\EntriesController
 */
class EntriesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

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

        'app.Questions',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EntriesController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/entries');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EntriesController::view()
     */
    public function testView(): void
    {
        $this->get('/entries/view/2342ad37-13f0-4fd1-bd3f-2032273626ce');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\EntriesController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/entries/add', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Controller Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'controller@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => '',
        ]);

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame(1, $entries->find()->where(['entry_name' => 'Controller Entry'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\EntriesController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/entries/edit/2342ad37-13f0-4fd1-bd3f-2032273626ce', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Updated Entry',
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 1,
            'entry_email' => 'updated@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $this->assertSame('Updated Entry', $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce')->entry_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\EntriesController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/entries/delete/2342ad37-13f0-4fd1-bd3f-2032273626ce');

        $this->assertRedirectContains('/entries');
        $entries = $this->getTableLocator()->get('Entries');
        $deleted = $entries->find('withTrashed')->where(['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testLookupOptions(): void
    {
        $this->configRequest([
            'headers' => [
                'Origin' => 'http://localhost',
                'Access-Control-Request-Method' => 'POST',
            ],
        ]);
        $this->options('/lookup.json');

        $this->assertResponseOk();
        $this->assertResponseContains('OPTIONS YES');
    }

    public function testLookupRejectsInvalidData(): void
    {
        $this->post('/lookup.json', [
            'reference_number' => 'abc',
            'security_code' => 'Lor',
        ]);

        $this->assertResponseCode(400);
        $this->assertResponseContains('Invalid Lookup Data');
    }

    public function testLookupReturnsEntry(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Lookup Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'lookup@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $this->assertNotFalse($entries->save($entry));

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => $entry->security_code,
        ]);

        $this->assertResponseOk();
        $this->assertResponseContains((string)$entry->reference_number);
        $this->assertResponseContains($entry->entry_name);
        $this->assertResponseNotContains($entry->entry_email);
        $this->assertResponseNotContains($entry->security_code);
    }

    public function testLookupReturnsNotFoundForWrongSecurityCode(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'entry_name' => 'Lookup Failure Entry',
            'active' => true,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'entry_email' => 'lookup-fail@example.com',
            'entry_mobile' => '07123456789',
            'security_code' => 'ABCDE',
        ]);
        $this->assertNotFalse($entries->save($entry));

        $this->post('/lookup.json', [
            'reference_number' => $entry->reference_number,
            'security_code' => 'WRONG',
        ]);

        $this->assertResponseCode(404);
        $this->assertResponseContains('Invalid Lookup');
    }
}
