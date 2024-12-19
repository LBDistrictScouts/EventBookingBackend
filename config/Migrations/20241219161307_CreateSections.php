<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateSections extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('sections');
        $table->addColumn('section_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('participant_type_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_section_participant_types', 'participant_type_id', 'participant_types', 'id', []);
        $table->addColumn('group_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKeyWithName('fk_section_groups', 'group_id', 'groups', 'id', []);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('deleted', 'timestamp', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();
    }
}
