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
                'id' => 1,
                'group_name' => 'Lorem ipsum dolor sit amet',
                'visible' => 1,
                'created' => 1737037152,
                'modified' => 1737037152,
                'deleted' => 1737037152,
            ],
        ];
        parent::init();
    }
}
