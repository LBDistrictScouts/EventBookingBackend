<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SectionsController Test Case
 *
 * @uses \App\Controller\SectionsController
 */
class SectionsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\SectionsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/sections/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('sections', $data);
        $this->assertCount(1, $data['sections']);
        $this->assertArrayNotHasKey('notification_email', $data['sections'][0]);
    }

    public function testPublicIndexJsonDoesNotRequireAuthentication(): void
    {
        $this->session([]);

        $this->get('/sections.json');

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('sections', $data);
        $this->assertCount(1, $data['sections']);
        $this->assertArrayNotHasKey('notification_email', $data['sections'][0]);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\SectionsController::view()
     */
    public function testView(): void
    {
        $this->get('/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\SectionsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/sections/add', [
            'section_name' => 'Explorers',
            'notification_email' => 'explorers@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => '',
            'events' => [
                '_ids' => [
                    '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                ],
            ],
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->get(
            $sections->find()->select(['id'])->where(['section_name' => 'Explorers'])->firstOrFail()->id,
            contain: ['Events'],
        );
        $this->assertSame('explorers@example.com', $section->notification_email);
        $this->assertNull($section->osm_section_id);
        $this->assertCount(1, $section->events);
        $this->assertSame('3a6d9419-b621-45cf-a13e-4db9647bf5bc', $section->events[0]->id);
    }

    public function testAddFormRendersNotificationEmailAndOsmSectionIdInputs(): void
    {
        $this->get('/sections/add');

        $this->assertResponseOk();
        $this->assertResponseContains('name="notification_email"');
        $this->assertResponseContains('type="email"');
        $this->assertResponseContains('name="osm_section_id"');
        $this->assertResponseContains('type="number"');
        $this->assertResponseNotContains('<select name="osm_section_id"');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\SectionsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/sections/edit/95116a77-0675-4e1a-9d0c-74e3d40d92c1', [
            'section_name' => 'Renamed Section',
            'notification_email' => 'renamed@example.com',
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => '',
            'events' => [
                '_ids' => [
                    '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                ],
            ],
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $section = $sections->get('95116a77-0675-4e1a-9d0c-74e3d40d92c1', contain: ['Events']);
        $this->assertSame('Renamed Section', $section->section_name);
        $this->assertSame('renamed@example.com', $section->notification_email);
        $this->assertNull($section->osm_section_id);
        $this->assertCount(1, $section->events);
        $this->assertSame('3a6d9419-b621-45cf-a13e-4db9647bf5bc', $section->events[0]->id);
    }

    public function testEditFormRendersCurrentNotificationEmailAndOsmSectionIdInputs(): void
    {
        $this->get('/sections/edit/95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertResponseOk();
        $this->assertResponseContains('name="notification_email"');
        $this->assertResponseContains('value="section@example.com"');
        $this->assertResponseContains('name="osm_section_id"');
        $this->assertResponseContains('type="number"');
        $this->assertResponseNotContains('<select name="osm_section_id"');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\SectionsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/sections/delete/95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $deleted = $sections->find('withTrashed')->where(['id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
