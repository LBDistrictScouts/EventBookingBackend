<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterParticipantTypesTypeCode extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('participant_types');
        $table->addColumn('osm_type_code', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => true,
        ]);
        $table->update();
    }
}
