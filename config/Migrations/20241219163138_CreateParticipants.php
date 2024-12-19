<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateParticipants extends BaseMigration
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
        $table = $this->table('participants');
        $table->addColumn('first_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('last_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('entry_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName(
            'fk_participant_entries',
            'entry_id',
            'entries',
            'id',
        );
        $table->addColumn('participant_type_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName(
            'fk_participant_participant_types',
            'participant_type_id',
            'participant_types',
            'id',
        );
        $table->addColumn('section_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addForeignKeyWithName(
            'fk_participant_sections',
            'section_id',
            'sections',
            'id',
        );
        $table->addColumn('checked_in', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('checked_out', 'boolean', [
            'default' => false,
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
     * Reverts the changes made to the 'participants' table by dropping it.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('participants');
        $table->drop()->save();
    }
}
