<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateGroups extends BaseMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('groups');
        $table->addColumn('group_name', 'string', [
            'limit' => 64,
            'null' => false,
        ]);
        $table->addIndex(['group_name'], ['unique' => true]);
        $table->addColumn('visible', 'boolean', [
            'default' => true,
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
     * Down Method.
     *
     * This method reverts the changes made by the up method by dropping the 'groups' table.
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('groups');
        $table->drop()->save();
    }
}
