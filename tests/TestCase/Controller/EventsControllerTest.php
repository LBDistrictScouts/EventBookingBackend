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
     * Test view method
     *
     * @return void
     * @uses \App\Controller\EventsController::view()
     */
    public function testView(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\EventsController::add()
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\EventsController::edit()
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\EventsController::delete()
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
