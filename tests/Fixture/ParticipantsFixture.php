<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ParticipantsFixture
 */
class ParticipantsFixture extends TestFixture
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
                'id' => '5045fd83-55db-4d36-8a8a-63222e50e3fd',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'entry_id' => '739c4766-42e2-4194-9fde-e38ae2c45189',
                'participant_type_id' => '1a1decfb-75ca-4e8f-8d5d-3bc7ede77b22',
                'section_id' => '6486939a-e47b-40a8-89c6-865113d3aa4f',
                'checked_in' => 1,
                'checked_out' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => 1737039597,
                'highest_check_in_sequence' => 1,
            ],
        ];
        parent::init();
    }
}
