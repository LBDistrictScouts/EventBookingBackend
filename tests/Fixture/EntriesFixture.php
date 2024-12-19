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
                'id' => 1,
                'event_id' => 1,
                'entry_name' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1734627158,
                'modified' => 1734627158,
                'deleted' => 1734627158,
                'entry_email' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
