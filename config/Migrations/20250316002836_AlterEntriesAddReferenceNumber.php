<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlterEntriesAddReferenceNumber extends BaseMigration
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
        $table = $this->table('entries');
        $table->addColumn('reference_number', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();

        $this->execute('WITH ranked AS (
                SELECT id, ROW_NUMBER() OVER (PARTITION BY event_id ORDER BY created ASC) AS new_ref
                FROM entries
            )
            UPDATE entries
            SET reference_number = ranked.new_ref
            FROM ranked
            WHERE entries.id = ranked.id;');

        $this->table('entries')
            ->addIndex(
                ['event_id', 'reference_number'],
                ['unique' => true, 'name' => 'entries_env_ref_unique']
            )
            ->update();
    }
}
