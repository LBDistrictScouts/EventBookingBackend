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
     *
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('sections', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', ['null' => false]);
        $table->addColumn('section_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('participant_type_id', 'uuid', ['null' => false]);
        $table->addForeignKeyWithName(
            'fk_section_participant_types',
            'participant_type_id',
            'participant_types',
            'id',
        );
        $table->addColumn('group_id', 'uuid', ['null' => false]);
        $table->addForeignKeyWithName(
            'fk_section_groups',
            'group_id',
            'groups',
            'id',
        );
        $table->addColumn('osm_section_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addIndex(['osm_section_id'], ['unique' => true]);
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

    /**
     * Revert Method.
     *
     * This method reverts the changes made by the up() method. It drops the 'sections' table.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('sections');
        $table->drop()->save();
    }
}
