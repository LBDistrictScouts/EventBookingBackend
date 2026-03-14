<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\CheckInsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CheckInsController Test Case
 *
 * @uses \App\Controller\CheckInsController
 */
class CheckInsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/check-ins');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::view()
     */
    public function testView(): void
    {
        $this->get('/check-ins/view/2172aa66-e48c-4026-aa73-e6674a3d9926');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/check-in.json', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2025-01-16 12:00:00',
            'participants' => ['5045fd83-55db-4d36-8a8a-63222e50e3fd'],
        ]);

        $this->assertResponseOk();
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $this->assertGreaterThan(1, $checkIns->find()->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/check-ins/edit/2172aa66-e48c-4026-aa73-e6674a3d9926', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'check_in_time' => '2025-01-16 13:00:00',
            'participant_count' => 1,
        ]);

        $this->assertRedirectContains('/check-ins');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $this->assertSame('2025-01-16 13:00:00', $checkIns->get('2172aa66-e48c-4026-aa73-e6674a3d9926')->check_in_time->format('Y-m-d H:i:s'));
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\CheckInsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/check-ins/delete/2172aa66-e48c-4026-aa73-e6674a3d9926');

        $this->assertRedirectContains('/check-ins');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $deleted = $checkIns->find('withTrashed')->where(['id' => '2172aa66-e48c-4026-aa73-e6674a3d9926'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }

    public function testAddOptionsRequest(): void
    {
        $this->configRequest([
            'headers' => [
                'Origin' => 'http://localhost',
                'Access-Control-Request-Method' => 'POST',
            ],
        ]);
        $this->options('/check-in.json');

        $this->assertResponseOk();
        $this->assertResponseContains('OPTIONS YES');
    }

    public function testAddPageLoadsForSpecificEntry(): void
    {
        $this->get('/check-ins/add/2342ad37-13f0-4fd1-bd3f-2032273626ce');

        $this->assertResponseOk();
        $this->assertResponseContains('checkpoint-id');
        $this->assertResponseContains('participants');
    }

    public function testCheckpointPageLoads(): void
    {
        $this->disableErrorHandlerMiddleware();
        $this->expectException(\Cake\View\Exception\MissingTemplateException::class);
        $this->get('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7');
    }

    public function testCheckpointPostCreatesCheckIn(): void
    {
        $this->enableFormTokens();
        $this->post('/check-ins/checkpoint/8454694e-a2f3-4775-b75d-1fd3e57cc4b7', [
            'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
            'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            'participants' => ['5045fd83-55db-4d36-8a8a-63222e50e3fd'],
        ]);

        $this->assertRedirectContains('/entries/view/2342ad37-13f0-4fd1-bd3f-2032273626ce');
        $checkIns = $this->getTableLocator()->get('CheckIns');
        $this->assertGreaterThan(1, $checkIns->find()->count());
    }
}
