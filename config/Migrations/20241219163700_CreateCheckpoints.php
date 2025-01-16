<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateCheckpoints extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('checkpoints', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', ['null' => false]);
        $table->addColumn('checkpoint_sequence', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('checkpoint_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('event_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName(
            'fk_checkpoint_events',
            'event_id',
            'events',
            'id',
        );
        $table->addIndex(['event_id', 'checkpoint_sequence'], ['unique' => true]);
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
     * Revert Method.
     *
     * Reverts the changes made in the up() method by dropping the 'checkpoints' table.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('checkpoints');
        $table->drop()->save();
    }
}
