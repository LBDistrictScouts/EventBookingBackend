<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterEntriesAddReminderSent extends BaseMigration
{
    public function change(): void
    {
        $this->table('entries')
            ->addColumn('reminder_sent', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->update();
    }
}
