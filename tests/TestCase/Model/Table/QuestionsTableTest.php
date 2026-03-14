<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuestionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuestionsTable Test Case
 */
class QuestionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuestionsTable
     */
    protected $Questions;

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
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Questions') ? [] : ['className' => QuestionsTable::class];
        $this->Questions = $this->getTableLocator()->get('Questions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Questions);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\QuestionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $question = $this->Questions->newEntity([
            'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
            'question_text' => 'What is your emergency contact?',
            'answer_text' => 'Please provide a name and number.',
        ]);

        $this->assertEmpty($question->getErrors());

        $invalid = $this->Questions->newEntity([
            'event_id' => 'not-a-uuid',
            'question_text' => '',
            'answer_text' => '',
        ]);

        $this->assertArrayHasKey('event_id', $invalid->getErrors());
        $this->assertArrayHasKey('question_text', $invalid->getErrors());
        $this->assertArrayHasKey('answer_text', $invalid->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\QuestionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $invalid = $this->Questions->newEntity([
            'event_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'question_text' => 'Question',
            'answer_text' => 'Answer',
        ]);

        $this->assertFalse($this->Questions->save($invalid));
        $this->assertNotEmpty($invalid->getError('event_id'));
    }
}
