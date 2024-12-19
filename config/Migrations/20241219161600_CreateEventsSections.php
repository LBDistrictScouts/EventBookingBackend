<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEventsSections extends BaseMigration
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
        $table = $this->table('events_sections');
        $table->addColumn('section_id', 'integer', [
            'autoIncrement' => false,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('event_id', 'integer', [
            'autoIncrement' => false,
            'default' => null,
            'limit' => 11,
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
        $table->addColumn('deleted', 'timestamp', [
            'default' => null,
            'null' => true,
        ]);
        $table->addPrimaryKey([
            'section_id',
            'event_id',
        ]);
        $table->create();
    }
}