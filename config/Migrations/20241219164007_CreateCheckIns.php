<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateCheckIns extends BaseMigration
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
        $table = $this->table('check_ins');
        $table->addColumn('checkpoint_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName(
            'fk_check_in_checkpoints',
            'checkpoint_id',
            'checkpoints',
            'id',
        );
        $table->addColumn('entry_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName(
            'fk_check_in_entries',
            'entry_id',
            'entries',
            'id',
        );
        $table->addColumn('check_in_time', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('participant_count', 'integer', [
            'default' => null,
            'limit' => 11,
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
     * This method is used to revert the migration, dropping the previously created table.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('check_ins');
        $table->drop()->save();
    }
}
