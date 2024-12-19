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
                'id' => 1,
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'entry_id' => 1,
                'participant_type_id' => 1,
                'section_id' => 1,
                'checked_in' => 1,
                'checked_out' => 1,
                'created' => 1734627166,
                'modified' => 1734627166,
                'deleted' => 1734627166,
            ],
        ];
        parent::init();
    }
}
