<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ParticipantTypesFixture
 */
class ParticipantTypesFixture extends TestFixture
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
                'participant_type' => 'Lorem ipsum dolor sit amet',
                'adult' => 1,
                'uniformed' => 1,
                'out_of_district' => 1,
                'created' => 1734627322,
                'modified' => 1734627322,
                'deleted' => 1734627322,
            ],
        ];
        parent::init();
    }
}
