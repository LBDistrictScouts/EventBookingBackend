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
                'id' => '2172aa66-e48c-4026-aa73-e6674a3d9926',
                'checkpoint_id' => '8454694e-a2f3-4775-b75d-1fd3e57cc4b7',
                'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
                'check_in_time' => 1737039597,
                'participant_count' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
