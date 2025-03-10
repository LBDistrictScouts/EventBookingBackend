<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EntriesFixture
 */
class EntriesFixture extends TestFixture
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
                'id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
                'event_id' => '3a6d9419-b621-45cf-a13e-4db9647bf5bc',
                'entry_name' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1739184246,
                'modified' => 1739184246,
                'deleted' => null,
                'entry_email' => 'Lorem ipsum dolor sit amet',
                'entry_mobile' => 'Lorem ipsum dolor ',
                'security_code' => 'Lor',
            ],
        ];
        parent::init();
    }
}
