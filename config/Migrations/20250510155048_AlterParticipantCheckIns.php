<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterParticipantCheckIns extends BaseMigration
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
        $table = $this->table('participants_check_ins');
        $table->removeColumn('id');
        $table->update();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('participants_check_ins');
        $table->addColumn('id', 'integer', ['null' => true, 'default' => null]);
        $table->update();
    }
}
