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
                'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
                'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
                'section_id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
                'checked_in' => 1,
                'checked_out' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => null,
                'highest_check_in_sequence' => 1,
            ],
        ];
        parent::init();
    }
}
