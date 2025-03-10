<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CheckpointsFixture
 */
class CheckpointsFixture extends TestFixture
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
                'id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
                'checkpoint_sequence' => 1,
                'checkpoint_name' => 'Lorem ipsum dolor sit amet',
                'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
