<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateParticipantsCheckIns extends BaseMigration
{
    public bool $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('participants_check_ins');
        $table->addColumn('check_in_id', 'integer', [
            'autoIncrement' => false,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_participant_check_in_check_ins', 'check_in_id', 'check_ins', 'id', []);
        $table->addColumn('participant_id', 'integer', [
            'autoIncrement' => false,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_participant_check_in_participants', 'participant_id', 'participants', 'id', []);
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
        $table->addPrimaryKey([
            'check_in_id',
            'participant_id',
        ]);
        $table->create();
    }
}