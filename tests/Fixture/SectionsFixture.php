<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SectionsFixture
 */
class SectionsFixture extends TestFixture
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
                'section_name' => 'Lorem ipsum dolor sit amet',
                'participant_type_id' => 1,
                'group_id' => 1,
                'osm_section_id' => 1,
                'created' => 1737037153,
                'modified' => 1737037153,
                'deleted' => 1737037153,
            ],
        ];
        parent::init();
    }
}
