<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\QuestionsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\QuestionsController Test Case
 *
 * @uses \App\Controller\QuestionsController
 */
class QuestionsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthSessionTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Questions',
        'app.Events',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::index()
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
    }

    public function testIndex(): void
    {
        $this->get('/questions');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::view()
     */
    public function testView(): void
    {
        $this->get('/questions/view/3a6d9419-b621-45cf-a13e-4db9647bf5bc');
        $this->assertResponseOk();
        $this->assertResponseContains('Convallis morbi fringilla');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::add()
     */
    public function testAdd(): void
    {
        $this->enableFormTokens();
        $this->post('/questions/add', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'question_text' => 'Dietary requirements?',
            'answer_text' => 'Please list them.',
        ]);

        $this->assertRedirectContains('/questions');
        $questions = $this->getTableLocator()->get('Questions');
        $this->assertSame(1, $questions->find()->where(['question_text' => 'Dietary requirements?'])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::edit()
     */
    public function testEdit(): void
    {
        $this->enableFormTokens();
        $this->post('/questions/edit/3a6d9419-b621-45cf-a13e-4db9647bf5bc', [
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'question_text' => 'Updated Question',
            'answer_text' => 'Updated Answer',
        ]);

        $this->assertRedirectContains('/questions');
        $questions = $this->getTableLocator()->get('Questions');
        $this->assertSame('Updated Question', $questions->get('3a6d9419-b621-45cf-a13e-4db9647bf5bc')->question_text);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::delete()
     */
    public function testDelete(): void
    {
        $this->enableFormTokens();
        $this->delete('/questions/delete/3a6d9419-b621-45cf-a13e-4db9647bf5bc');

        $this->assertRedirectContains('/questions');
        $questions = $this->getTableLocator()->get('Questions');
        $deleted = $questions->find('withTrashed')->where(['id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc'])->firstOrFail();
        $this->assertNotNull($deleted->deleted);
    }
}
