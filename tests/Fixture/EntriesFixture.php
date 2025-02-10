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
                'event_id' => '3dbd85d9-7b67-4f59-aaec-6186c3c33858',
                'entry_name' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'participant_count' => 1,
                'checked_in_count' => 1,
                'created' => 1739184246,
                'modified' => 1739184246,
                'deleted' => 1739184246,
                'entry_email' => 'Lorem ipsum dolor sit amet',
                'entry_mobile' => 'Lorem ipsum dolor ',
                'security_code' => 'Lor',
            ],
        ];
        parent::init();
    }
}
