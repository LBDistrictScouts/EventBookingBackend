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
                'created' => 1737037152,
                'modified' => 1737037152,
                'deleted' => 1737037152,
                'external_id' => 'be6e70e5-4c7c-4cf9-94d6-ad09bbc352a1',
            ],
        ];
        parent::init();
    }
}
