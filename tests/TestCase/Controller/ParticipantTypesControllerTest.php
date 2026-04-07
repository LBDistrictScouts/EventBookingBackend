<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ParticipantTypesController Test Case
 *
 * @uses \App\Controller\ParticipantTypesController
 */
class ParticipantTypesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.ParticipantTypes',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\ParticipantTypesController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/participant-types/index.json');
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('participantTypes', $data);
        $this->assertCount(1, $data['participantTypes']);
    }

    public function testPublicIndexJsonDoesNotRequireAuthentication(): void
    {
        $this->session([]);

        $this->get('/participant-types.json');

        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('participantTypes', $data);
        $this->assertCount(1, $data['participantTypes']);
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ParticipantTypesController::view()
     */
    public function testView(): void
    {
        $this->get('/participant-types/view/ea1e3a48-494b-4af7-bec0-6dbee60a40c0');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ParticipantTypesController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/participant-types/add', [
            'participant_type' => 'Young Leader',
            'adult' => true,
            'uniformed' => true,
            'out_of_district' => false,
            'category' => 1,
            'sort_order' => 2,
        ]);

        $this->assertRedirectContains('/participant-types');
        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $this->assertSame(1, $participantTypes->find()->where(['participant_type' => 'Young Leader'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\ParticipantTypesController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/participant-types/edit/ea1e3a48-494b-4af7-bec0-6dbee60a40c0', [
            'participant_type' => 'Updated Type',
            'adult' => true,
            'uniformed' => true,
            'out_of_district' => true,
            'category' => 0,
            'sort_order' => 1,
        ]);

        $this->assertRedirectContains('/participant-types');
        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $this->assertSame('Updated Type', $participantTypes->get('ea1e3a48-494b-4af7-bec0-6dbee60a40c0')->participant_type);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\ParticipantTypesController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/participant-types/delete/ea1e3a48-494b-4af7-bec0-6dbee60a40c0');

        $this->assertRedirectContains('/participant-types');
        $participantTypes = $this->getTableLocator()->get('ParticipantTypes');
        $deleted = $participantTypes->find('withTrashed')->where(['id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
