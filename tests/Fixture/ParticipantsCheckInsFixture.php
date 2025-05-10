<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ParticipantsCheckInsFixture
 */
class ParticipantsCheckInsFixture extends TestFixture
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
                'check_in_id' => '2172aa66-e48c-4026-aa73-e6674a3d9926',
                'participant_id' => '5045fd83-55db-4d36-8a8a-63222e50e3fd',
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
