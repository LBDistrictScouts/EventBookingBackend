<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterParticipants extends BaseMigration
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
        $table->addColumn('highest_check_in_sequence', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }

    /**
     * Revert Method.
     *
     * Reverts the changes applied in the up method by removing the specified column
     * from the participants table.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('participants');
        $table->removeColumn('highest_check_in_sequence');
        $table->update();
    }
}
