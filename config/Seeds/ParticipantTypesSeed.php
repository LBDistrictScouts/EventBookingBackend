<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * ParticipantTypes seed.
 */
class ParticipantTypesSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/5/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'd87c9eca-011c-49d9-9d18-040ab2503450',
                'participant_type' => 'Beaver',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-16 16:14:01.875873',
                'modified' => '2025-03-07 00:07:46.566129',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 2,
            ],
            [
                'id' => '826c9a76-7d16-466a-8cdc-0cadeb96de85',
                'participant_type' => 'Cub',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-16 16:13:35.469986',
                'modified' => '2025-03-07 00:08:05.816527',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 3,
            ],
            [
                'id' => 'ad35a223-b9e1-4bcf-a28f-ddf7bb1dfcbd',
                'participant_type' => 'Scout',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-20 11:17:21.031584',
                'modified' => '2025-03-07 00:08:12.679843',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 4,
            ],
            [
                'id' => '36be3cd8-5b54-4080-a05d-f665a2f33c2c',
                'participant_type' => 'Leader / Volunteer',
                'adult' => true,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-16 16:14:10.395758',
                'modified' => '2025-03-07 00:08:25.652254',
                'deleted' => null,
                'category' => 1,
                'sort_order' => 11,
            ],
            [
                'id' => '118cbd8a-aede-4ae1-81e3-675392e82cd6',
                'participant_type' => 'Squirrel',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-03-07 00:07:58.598873',
                'modified' => '2025-03-07 00:08:35.139368',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 1,
            ],
            [
                'id' => '7474361d-03ff-44fc-9a02-388e6c0d688c',
                'participant_type' => 'Parent / Non Uniformed Volunteer',
                'adult' => true,
                'uniformed' => false,
                'out_of_district' => false,
                'created' => '2025-03-07 00:08:52.047478',
                'modified' => '2025-03-07 00:08:52.047575',
                'deleted' => null,
                'category' => 1,
                'sort_order' => 12,
            ],
            [
                'id' => '51e74738-ddaf-40da-b6f6-5b2049f37136',
                'participant_type' => 'Dog',
                'adult' => false,
                'uniformed' => false,
                'out_of_district' => false,
                'created' => '2025-01-20 11:15:42.30767',
                'modified' => '2025-03-07 00:09:04.119629',
                'deleted' => null,
                'category' => 2,
                'sort_order' => 99,
            ],
            [
                'id' => 'fd7b3d07-5d7c-436a-92a7-3855a12e5fd9',
                'participant_type' => 'Explorer',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-03-10 12:19:12.20304',
                'modified' => '2025-03-10 12:19:12.203173',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 5,
            ],
        ];

        $table = $this->table('participant_types');
        $table->insert($data)->save();
    }
}
