<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CheckInsFixture
 */
class CheckInsFixture extends TestFixture
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
                'checkpoint_id' => 1,
                'entry_id' => 1,
                'check_in_time' => 1737037152,
                'participant_count' => 1,
                'created' => 1737037152,
                'modified' => 1737037152,
                'deleted' => 1737037152,
            ],
        ];
        parent::init();
    }
}
