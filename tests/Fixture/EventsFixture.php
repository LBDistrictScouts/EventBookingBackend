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
                'id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                'event_name' => 'Lorem ipsum dolor sit amet',
                'event_description' => 'Lorem ipsum dolor sit amet',
                'booking_code' => 'Lorem ipsum dolor ',
                'start_time' => 1737039597,
                'bookable' => true,
                'finished' => false,
                'entry_count' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
