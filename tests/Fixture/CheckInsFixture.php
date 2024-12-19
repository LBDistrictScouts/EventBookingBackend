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
                'check_in_time' => 1734627158,
                'participant_count' => 1,
                'created' => 1734627158,
                'modified' => 1734627158,
                'deleted' => 1734627158,
            ],
        ];
        parent::init();
    }
}
