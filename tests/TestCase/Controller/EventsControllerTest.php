<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\EventsController Test Case
 *
 * @uses \App\Controller\EventsController
 */
class EventsControllerTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EventsController::index()
     */
    public function testIndex(): void
    {
        $this->session([
            'Config' => [
                'time' => 1742087458,
            ],
            'Auth' => [
                'User' => [
                    'email' => 'jacob@lbdscouts.org.uk',
                    'subject' => '712277bf-88bc-4ad5-a87d-f3b4fd0051d5',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'token' => 'fake.token.goat-iIYoB5LwpHY5-dhcML-zRiFXsQFtHjedjUO7D2yJhjcLkxIVH3Qy7uIEenjcqyYKuTA0wVrm11N25II0UdBeazEEjEHgGZCO6Vf9ZFoJRtkw_rjHMR9LlpGbE0Z53hxifRjCXXMZNlX3GJQb80PQbPaU8FUq21EPPGLCZhni-Bdl4ZJeQtVOrretAk8HUBLdXw',
                ],
                'expires_at' => 1742088064,
            ],
        ]);

        $this->get('/events/index.json');
        $this->assertResponseOk();

        $resultData = json_decode($this->_response->getBody()->__toString(), true);

        $this->assertArrayHasKey('events', $resultData);
        $this->assertCount(1, $resultData['events']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EventsController::view()
     */
    public function testCurrent(): void
    {
        $this->get('/events/current.json');
        $this->assertResponseOk();

        $resultData = json_decode($this->_response->getBody()->__toString(), true);
        $this->assertArrayHasKey('event', $resultData);
        $this->assertCount(10, $resultData['event']);

        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->first();
        $event->set('finished', true);
        $events->save($event);

        $this->get('/events/current.json');
        $this->assertResponseError();
    }

    /**
     * @return void
     * @uses \App\Controller\EventsController::current()
     */
    public function testCurrentRendersSetupDashboardWhenNoCurrentEventExists(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->firstOrFail();
        $event->set('finished', true);
        $events->saveOrFail($event);

        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('No active event is configured yet');
        $this->assertResponseContains('/events/add');
        $this->assertResponseContains('/groups/add');
        $this->assertResponseContains('/sections/add');
        $this->assertResponseContains('/participant-types/add');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EventsController::view()
     */
    public function testView(): void
    {
        $this->get('/events/view/3a6d9419-b621-45cf-a13e-4db9647bf5bc.json');
        $this->assertResponseOk();

        $resultData = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('event', $resultData);
        $this->assertSame('Lorem ipsum dolor sit amet', $resultData['event']['event_name']);
        $this->assertArrayHasKey('sections', $resultData['event']);
        $this->assertArrayHasKey('questions', $resultData['event']);
        $this->assertArrayHasKey('checkpoints', $resultData['event']);
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\EventsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/events/add', [
            'event_name' => 'Controller Event',
            'event_description' => 'A new event',
            'booking_code' => 'CTRL2026',
            'start_time' => '2026-03-20 10:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 0,
            'participant_count' => 0,
            'checked_in_count' => 0,
            'sections' => ['_ids' => ['95116a77-0675-4e1a-9d0c-74e3d40d92c1']],
        ]);

        $this->assertRedirectContains('/events');
        $events = $this->getTableLocator()->get('Events');
        $this->assertSame(1, $events->find()->where(['event_name' => 'Controller Event'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\EventsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/events/edit/3a6d9419-b621-45cf-a13e-4db9647bf5bc', [
            'event_name' => 'Updated Event',
            'event_description' => 'Updated description',
            'booking_code' => 'Lorem ipsum dolor ',
            'start_time' => '2025-01-16 09:00:00',
            'bookable' => true,
            'finished' => false,
            'entry_count' => 1,
            'participant_count' => 1,
            'checked_in_count' => 1,
            'sections' => ['_ids' => ['95116a77-0675-4e1a-9d0c-74e3d40d92c1']],
        ]);

        $this->assertRedirectContains('/events');
        $events = $this->getTableLocator()->get('Events');
        $this->assertSame('Updated Event', $events->get('3a6d9419-b621-45cf-a13e-4db9647bf5bc')->event_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\EventsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/events/delete/3a6d9419-b621-45cf-a13e-4db9647bf5bc');

        $this->assertRedirectContains('/events');
        $events = $this->getTableLocator()->get('Events');
        $deleted = $events->find('withTrashed')->where(['id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
