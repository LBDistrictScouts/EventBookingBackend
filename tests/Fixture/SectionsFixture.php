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
                'id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
                'section_name' => 'Lorem ipsum dolor sit amet',
                'participant_type_id' => 'ea1e3a48-494b-4af7-bec0-6dbee60a40c0',
                'group_id' => '873b0f71-5389-46f9-baae-7d4855406b64',
                'osm_section_id' => 1,
                'created' => 1737039597,
                'modified' => 1737039597,
                'deleted' => 1737039597,
            ],
        ];
        parent::init();
    }
}
