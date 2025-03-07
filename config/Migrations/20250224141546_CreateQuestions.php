<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateQuestions extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('questions', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', ['null' => false]);
        $table->addColumn('event_id', 'uuid', [
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_questions_events',  'event_id', 'events', 'id');
        $table->addColumn('question_text', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('answer_text', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('deleted', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('questions');
        $table->drop()->save();
    }
}
