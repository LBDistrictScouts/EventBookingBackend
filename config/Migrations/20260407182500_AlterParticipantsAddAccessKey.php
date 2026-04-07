<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterParticipantsAddAccessKey extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('participants');
        $table
            ->addColumn('access_key', 'uuid', [
                'after' => 'last_name',
                'default' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('participants');
        $table
            ->removeColumn('access_key')
            ->update();
    }
}
