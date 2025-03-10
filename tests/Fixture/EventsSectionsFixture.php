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
                'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
                'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
