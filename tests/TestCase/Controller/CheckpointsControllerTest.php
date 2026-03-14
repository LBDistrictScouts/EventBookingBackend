<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\CheckpointsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CheckpointsController Test Case
 *
 * @uses \App\Controller\CheckpointsController
 */
class CheckpointsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Checkpoints',
        'app.Events',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/checkpoints/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('checkpoints', $data);
        $this->assertCount(1, $data['checkpoints']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::view()
     */
    public function testView(): void
    {
        $this->get('/checkpoints/view/8454694e-a2f3-4775-b75d-1fd3e57cc4b7.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('checkpoint', $data);
        $this->assertSame('Lorem ipsum dolor sit amet', $data['checkpoint']['checkpoint_name']);
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/checkpoints/add', [
            'checkpoint_sequence' => 2,
            'checkpoint_name' => 'Finish',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $this->assertSame(1, $checkpoints->find()->where(['checkpoint_name' => 'Finish'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/checkpoints/edit/8454694e-a2f3-4775-b75d-1fd3e57cc4b7', [
            'checkpoint_sequence' => 1,
            'checkpoint_name' => 'Updated Checkpoint',
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
        ]);

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $this->assertSame('Updated Checkpoint', $checkpoints->get('8454694e-a2f3-4775-b75d-1fd3e57cc4b7')->checkpoint_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\CheckpointsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/checkpoints/delete/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');

        $this->assertRedirectContains('/checkpoints');
        $checkpoints = $this->getTableLocator()->get('Checkpoints');
        $deleted = $checkpoints->find('withTrashed')->where(['id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
