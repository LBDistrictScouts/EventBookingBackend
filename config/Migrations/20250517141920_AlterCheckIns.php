<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterCheckIns extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('check_ins');
        $table->changeColumn('participant_count', 'integer', [
            'default' => 0,
            'null' => false,
        ]);
        $table->update();
    }
}
