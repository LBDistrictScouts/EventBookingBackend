<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\GroupsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\GroupsController Test Case
 *
 * @uses \App\Controller\GroupsController
 */
class GroupsControllerTest extends TestCase
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
     * @uses \App\Controller\GroupsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/groups/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('groups', $data);
        $this->assertCount(1, $data['groups']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\GroupsController::view()
     */
    public function testView(): void
    {
        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\GroupsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/groups/add', [
            'group_name' => 'Integration Group',
            'visible' => true,
            'sort_order' => 2,
        ]);

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $this->assertSame(1, $groups->find()->where(['group_name' => 'Integration Group'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\GroupsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/groups/edit/873b0f71-5389-46f9-baae-7d4855406b64', [
            'group_name' => 'Renamed Group',
            'visible' => true,
            'sort_order' => 3,
        ]);

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $this->assertSame('Renamed Group', $groups->get('873b0f71-5389-46f9-baae-7d4855406b64')->group_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\GroupsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/groups/delete/873b0f71-5389-46f9-baae-7d4855406b64');

        $this->assertRedirectContains('/groups');
        $groups = $this->getTableLocator()->get('Groups');
        $deleted = $groups->find('withTrashed')->where(['id' => '873b0f71-5389-46f9-baae-7d4855406b64'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testViewWithBillingQuery(): void
    {
        $this->get('/groups/view/873b0f71-5389-46f9-baae-7d4855406b64?event_id=3a6d9419-b621-45cf-a13e-4db9647bf5bc');

        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }
}
