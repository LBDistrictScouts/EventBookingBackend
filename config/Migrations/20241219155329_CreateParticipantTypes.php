<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateParticipantTypes extends BaseMigration
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
        $table = $this->table('participant_types');
        $table->addColumn('participant_type', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => false,
        ]);
        $table->addColumn('adult', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('uniformed', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('out_of_district', 'boolean', [
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
            'null' => false,
        ]);
        $table->create();
    }

    /**
     * Revert the migrations.
     *
     * This method is used to drop the 'participant_types' table, reversing changes applied during the migration.
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('participant_types');
        $table->drop()->save();
    }
}
