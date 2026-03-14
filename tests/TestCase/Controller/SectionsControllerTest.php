<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\SectionsController;
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
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 2,
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $this->assertSame(1, $sections->find()->where(['section_name' => 'Explorers'])->count());
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
            'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
            'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
            'osm_section_id' => 1,
        ]);

        $this->assertRedirectContains('/sections');
        $sections = $this->getTableLocator()->get('Sections');
        $this->assertSame('Renamed Section', $sections->get('95116a77-0675-4e1a-9d0c-74e3d40d92c1')->section_name);
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
