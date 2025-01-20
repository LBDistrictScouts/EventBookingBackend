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
                'id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
                'participant_type' => 'Lorem ipsum dolor sit amet',
                'adult' => 1,
                'uniformed' => 1,
                'out_of_district' => 1,
                'created' => 1737326741,
                'modified' => 1737326741,
                'deleted' => null,
                'category' => 0,
                'sort_order' => 1,
            ],
        ];
        parent::init();
    }
}
