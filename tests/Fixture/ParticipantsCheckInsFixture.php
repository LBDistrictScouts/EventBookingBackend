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
                'check_in_id' => 1,
                'participant_id' => 1,
                'created' => 1737037152,
                'modified' => 1737037152,
                'deleted' => 1737037152,
            ],
        ];
        parent::init();
    }
}
