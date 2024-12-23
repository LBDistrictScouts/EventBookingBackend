<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EventsFixture
 */
class EventsFixture extends TestFixture
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
                'event_name' => 'Lorem ipsum dolor sit amet',
                'event_description' => 'Lorem ipsum dolor sit amet',
                'booking_code' => 'Lorem ipsum dolor ',
                'start_time' => 1734629603,
                'bookable' => 1,
                'finished' => 1,
                'entry_count' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1734629603,
                'modified' => 1734629603,
                'deleted' => 1734629603,
            ],
        ];
        parent::init();
    }
}
