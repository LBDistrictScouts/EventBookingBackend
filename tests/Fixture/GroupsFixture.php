<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GroupsFixture
 */
class GroupsFixture extends TestFixture
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
                'id' => '873b0f71-5389-46f9-baae-7d4855406b64',
                'group_name' => 'Lorem ipsum dolor sit amet',
                'visible' => 1,
                'created' => 1739184233,
                'modified' => 1739184233,
                'deleted' => null,
                'sort_order' => 1,
            ],
        ];
        parent::init();
    }
}
