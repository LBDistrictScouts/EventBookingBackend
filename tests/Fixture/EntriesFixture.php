<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EntriesFixture
 */
class EntriesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => '937a1181-e099-442c-be4e-1b103d413c9f',
                'event_id' => 'f7692506-1349-4efa-86eb-10c26f0691f3',
                'entry_name' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
                'entry_email' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
