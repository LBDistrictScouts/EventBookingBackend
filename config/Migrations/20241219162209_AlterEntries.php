<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterEntries extends BaseMigration
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
        $table = $this->table('entries');
        $table->addColumn('entry_email', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->update();
    }

    /**
     * Revert Method.
     *
     * Reverts the changes made in the migration.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('entries');
        $table->removeColumn('entry_email');
        $table->update();
    }
}
