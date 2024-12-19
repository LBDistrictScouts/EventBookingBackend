<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEvents extends BaseMigration
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
        $table->addColumn('event_name', 'string', [
            'default' => null,
            'limit' => 64,
            'null' => false,
        ]);
        $table->addColumn('event_description', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('booking_code', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('start_time', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('bookable', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('finished', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('entry_count', 'integer', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('participant_count', 'integer', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('checked_in_count', 'integer', [
            'default' => 0,
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
     * This method is used to revert the changes made by the `up` method. It drops the `events` table from the database.
     *
     * @return void
     */
    public function down(): void
    {
        $this->table('events')->drop()->save();
    }
}
