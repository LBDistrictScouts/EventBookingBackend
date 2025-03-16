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
class BookingControllerTest extends TestCase
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
    public function testBook(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->first();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->first();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->first();

        $this->post(
            '/book.json',
            [
                'event_id' => $event->id,
                'entry_name' => 'Test My Booking',
                'entry_email' => 'jacob@this.com',
                'entry_mobile' => '08938 928197',
                'participants' => [
                    [
                        'access_key' => '2a235073-63c3-42fe-a12d-5429b21562e6',
                        'first_name' => 'Jacob',
                        'last_name' => 'Tyler',
                        'participant_type_id' => $participantType->id,
                        'section_id' => $section->id,
                    ],
                ],
            ]
        );

        $this->assertResponseOk();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\EventsController::index()
     */
    public function testBlankBook(): void
    {
        $events = $this->getTableLocator()->get('Events');
        $event = $events->find('all')->first();

        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $participantType = $participantTypes->find('all')->first();

        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->find('all')->first();

        $requestData = [
            'event_id' => $event->id,
            'entry_name' => 'Test My Booking',
            'entry_email' => 'jacob@this.com',
            'entry_mobile' => '08938 928197',
            'participants' => [
                [
                    'access_key' => '2a235073-63c3-42fe-a12d-5429b21562e6',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                ],
                [
                    'access_key' => 'bac8cfbf-d59a-49db-8d81-de424ba60f8e',
                    'first_name' => 'Joe',
                    'last_name' => 'Bloggs',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                    '' => '9aebb3ae-8fd6-40f5-be2b-3e823a8a8ca6',
                ],
                [
                    'access_key' => '02310d8b-3fe0-47dd-a5e6-b02412a4bcbc',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'participant_type_id' => $participantType->id,
                    'section_id' => $section->id,
                    '' => '9aebb3ae-8fd6-40f5-be2b-3e823a8a8ca6',
                ],
            ],
        ];

        $this->post('/book.json', $requestData);
        $this->assertResponseOk();

        $resultData = json_decode($this->_response->getBody()->__toString(), true);

        $expected = [
            'success' => true,
            'entry' => $requestData,
            'message' => 'Saved',
        ];

        $this->assertEmpty($resultData['errors']);
        $this->assertSame($expected['success'], $resultData['success']);
        $this->assertSame($expected['message'], $resultData['message']);

        $this->assertCount(3, $resultData['entry']['participants']);
        $this->assertCount(16, $resultData['entry']);

        $this->assertEquals($expected['entry']['entry_name'], $resultData['entry']['entry_name']);
        $this->assertEquals($expected['entry']['entry_email'], $resultData['entry']['entry_email']);
        $this->assertEquals($expected['entry']['entry_mobile'], $resultData['entry']['entry_mobile']);
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
