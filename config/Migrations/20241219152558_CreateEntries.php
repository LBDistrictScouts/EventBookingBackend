<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEntries extends BaseMigration
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
        $table = $this->table('entries', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', ['null' => false]);
        $table->addColumn('event_id', 'uuid', [
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_entry_events', 'event_id', 'events', 'id');
        $table->addColumn('entry_name', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => false,
        ]);
        $table->addIndex(['event_id', 'entry_name'], ['unique' => true]);
        $table->addColumn('active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('participant_count', 'integer', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('checked_in_count', 'integer', [
            'default' => 0,
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
     * Revert Method.
     *
     * Reverts the changes made in the migration by dropping the 'entries' table.
     *
     * @return void
     */
    public function down(): void
    {
        $this->table('entries')->drop()->save();
    }
}
