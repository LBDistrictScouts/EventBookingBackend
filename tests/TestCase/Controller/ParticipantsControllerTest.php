<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ParticipantsController Test Case
 *
 * @uses \App\Controller\ParticipantsController
 */
class ParticipantsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\ParticipantsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/participants');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Showing participants for the current event: Lorem ipsum dolor sit amet');
        $this->assertResponseContains('Show Deleted Participants');
    }

    public function testIndexCanShowAllParticipantsAcrossEvents(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->newEntity([
            'event_name' => 'Second Event',
            'event_description' => 'Second Event Description',
            'booking_code' => 'SECOND',
            'start_time' => '2027-01-20 09:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 1,
            'checked_in_count' => 0,
        ]);
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->newEntity([
            'event_id' => $event->id,
            'entry_name' => 'Second Event Team',
            'reference_number' => 2,
            'active' => true,
            'participant_count' => 1,
            'checked_in_count' => 0,
            'entry_email' => 'second@example.com',
            'entry_mobile' => '07000000000',
            'security_code' => 'XYZ12',
        ]);
        $entries->saveOrFail($entry);

        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->newEntity([
            'first_name' => 'Second',
            'last_name' => 'Walker',
            'entry_id' => $entry->id,
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);
        $participants->saveOrFail($participant);

        $this->get('/participants');
        $this->assertResponseOk();
        $this->assertResponseNotContains('Second Event Team');

        $this->get('/participants?all=1');
        $this->assertResponseOk();
        $this->assertResponseContains('Showing participants across all events.');
        $this->assertResponseContains('Second Event Team');
        $this->assertResponseContains('Second');
    }

    public function testIndexCanSearchParticipantsByParticipantName(): void
    {
        $this->get('/participants?participants_search=ipsum');

        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    public function testIndexCanSearchParticipantsByEntryName(): void
    {
        $this->get('/participants?participants_search=Lorem');

        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ParticipantsController::view()
     */
    public function testView(): void
    {
        $this->get('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ParticipantsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/participants/add/2342ad37-13f0-4fd1-bd3f-2032273626ce', [
            'first_name' => 'New',
            'last_name' => 'Participant',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);

        $this->assertRedirectContains('/entries/view/2342ad37-13f0-4fd1-bd3f-2032273626ce');
        $participants = $this->getTableLocator()->get('Participants');
        $this->assertSame(1, $participants->find()->where(['first_name' => 'New', 'last_name' => 'Participant'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\ParticipantsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/participants/edit/5045fd83-55db-4d36-8a8a-63222e50e3fd', [
            'first_name' => 'Updated',
            'last_name' => 'Participant',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'checked_in' => false,
            'checked_out' => false,
            'highest_check_in_sequence' => 0,
        ]);

        $this->assertRedirectContains('/participants');
        $participants = $this->getTableLocator()->get('Participants');
        $this->assertSame('Updated', $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd')->first_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\ParticipantsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/participants/delete/5045fd83-55db-4d36-8a8a-63222e50e3fd');

        $this->assertRedirectContains('/participants');
        $participants = $this->getTableLocator()->get('Participants');
        $deleted = $participants->find('withTrashed')->where(['id' => '5045fd83-55db-4d36-8a8a-63222e50e3fd'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testIndexHidesDeletedParticipantsByDefault(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->get('/participants');

        $this->assertResponseOk();
        $this->assertResponseNotContains('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseContains('Show Deleted Participants');
    }

    public function testIndexCanShowDeletedParticipants(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->get('/participants?deleted=1');

        $this->assertResponseOk();
        $this->assertResponseContains('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseContains('/participants/restore/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseContains('Hide Deleted Participants');
    }

    public function testIndexCanShowDeletedParticipantsAcrossAllEvents(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->get('/participants?all=1&deleted=1');

        $this->assertResponseOk();
        $this->assertResponseContains('Showing participants across all events.');
        $this->assertResponseContains('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseContains('Hide Deleted Participants');
    }

    public function testIndexCanSearchDeletedParticipants(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->get('/participants?deleted=1&participants_search=ipsum');

        $this->assertResponseOk();
        $this->assertResponseContains('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $this->assertResponseContains('Hide Deleted Participants');
    }

    public function testIndexCanSortDeletedParticipantSearchResults(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->get('/participants?deleted=1&participants_search=Lorem&sort=deleted&direction=asc');

        $this->assertResponseOk();
        $this->assertResponseContains('/participants/view/5045fd83-55db-4d36-8a8a-63222e50e3fd');
    }

    public function testRestore(): void
    {
        $participants = $this->getTableLocator()->get('Participants');
        $participant = $participants->get('5045fd83-55db-4d36-8a8a-63222e50e3fd');
        $participants->deleteOrFail($participant);

        $this->enableFormTokens();
        $this->post('/participants/restore/5045fd83-55db-4d36-8a8a-63222e50e3fd');

        $this->assertRedirectContains('/participants?deleted=1');
        $restored = $participants->find('withTrashed')
            ->where(['id' => '5045fd83-55db-4d36-8a8a-63222e50e3fd'])
            ->firstOrFail();
        $this->assertNull($restored->deleted);
    }

    public function testIndexCanFilterCheckedInParticipants(): void
    {
        $this->get('/participants?checked-in=1');

        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    public function testAddPageLoadsWithoutEntryId(): void
    {
        $this->get('/participants/add');

        $this->assertResponseOk();
        $this->assertResponseContains('participant-type-id');
        $this->assertResponseContains('Lorem ipsum dolor');
        $this->assertResponseContains('[1] Lorem ipsum dolor sit amet');
    }

    public function testAddPageLoadsForSpecificEntry(): void
    {
        $this->get('/participants/add/2342ad37-13f0-4fd1-bd3f-2032273626ce');

        $this->assertResponseOk();
        $this->assertResponseContains('section-id');
    }
}
