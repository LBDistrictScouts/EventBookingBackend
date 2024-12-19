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
                'id' => 1,
                'checkpoint_sequence' => 1,
                'checkpoint_name' => 'Lorem ipsum dolor sit amet',
                'event_id' => 1,
                'created' => 1734627158,
                'modified' => 1734627158,
                'deleted' => 1734627158,
            ],
        ];
        parent::init();
    }
}
