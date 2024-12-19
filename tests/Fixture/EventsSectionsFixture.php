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
                'section_id' => 1,
                'event_id' => 1,
                'created' => 1734627166,
                'modified' => 1734627166,
                'deleted' => 1734627166,
            ],
        ];
        parent::init();
    }
}
