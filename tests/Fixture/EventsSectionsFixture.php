<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EventsSectionsFixture
 */
class EventsSectionsFixture extends TestFixture
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
                'section_id' => '36ee029d-7de2-4a9e-8aca-9442e074b19b',
                'event_id' => 'de44ddf5-ebbe-4bdb-9241-1242a6bbf2d8',
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => 1737039597,
            ],
        ];
        parent::init();
    }
}
