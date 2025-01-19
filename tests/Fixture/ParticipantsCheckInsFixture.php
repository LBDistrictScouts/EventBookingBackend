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
                'id' => 'bb4c3640-5998-4e4c-8b9b-1f88146297e0',
                'check_in_id' => '2de88706-5c62-4f93-9567-b3352d0d0485',
                'participant_id' => '4f2e8c06-206c-4566-a77d-5250f085f0fa',
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => 1737039597,
            ],
        ];
        parent::init();
    }
}
