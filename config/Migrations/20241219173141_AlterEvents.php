<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterEvents extends BaseMigration
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
        $table = $this->table('events');
        $table->addIndex(['event_name'], ['unique' => true]);
        $table->addIndex(['booking_code'], ['unique' => true]);
        $table->update();
    }

    /**
     * Revert Method.
     *
     * Reverts changes made in the up method by removing specified indexes.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('events');
        $table->removeIndex(['event_name']);
        $table->removeIndex(['booking_code']);
    }
}
