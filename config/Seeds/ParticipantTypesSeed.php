<?php
declare(strict_types=1);

use Cake\Datasource\FactoryLocator;
use Migrations\BaseSeed;

/**
 * ParticipantTypes seed.
 */
class ParticipantTypesSeed extends BaseSeed
{
    /**
     * @return bool
     */
    public function isIdempotent(): bool
    {
        return true;
    }

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
        /** @var \App\Model\Table\ParticipantTypesTable $participantTypesTable */
        $participantTypesTable = FactoryLocator::get('Table')->get('ParticipantTypes');

        $data = [
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
                'osm_type_code' => null,
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
                'osm_type_code' => null,
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
                'osm_type_code' => null,
            ],
            [
                'id' => '118cbd8a-aede-4ae1-81e3-675392e82cd6',
                'participant_type' => 'Squirrel',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-03-07 00:07:58.598873',
                'modified' => '2026-03-31 22:47:03.711209',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 1,
                'osm_type_code' => 'earlyyears',
            ],
            [
                'id' => 'd87c9eca-011c-49d9-9d18-040ab2503450',
                'participant_type' => 'Beaver',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-16 16:14:01.875873',
                'modified' => '2026-03-31 22:47:12.558406',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 2,
                'osm_type_code' => 'beavers',
            ],
            [
                'id' => '826c9a76-7d16-466a-8cdc-0cadeb96de85',
                'participant_type' => 'Cub',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-16 16:13:35.469986',
                'modified' => '2026-03-31 22:47:25.187733',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 3,
                'osm_type_code' => 'cubs',
            ],
            [
                'id' => 'ad35a223-b9e1-4bcf-a28f-ddf7bb1dfcbd',
                'participant_type' => 'Scout',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-01-20 11:17:21.031584',
                'modified' => '2026-03-31 22:47:37.556124',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 4,
                'osm_type_code' => 'scouts',
            ],
            [
                'id' => 'fd7b3d07-5d7c-436a-92a7-3855a12e5fd9',
                'participant_type' => 'Explorer',
                'adult' => false,
                'uniformed' => true,
                'out_of_district' => false,
                'created' => '2025-03-10 12:19:12.20304',
                'modified' => '2026-03-31 22:47:50.24163',
                'deleted' => null,
                'category' => 0,
                'sort_order' => 5,
                'osm_type_code' => 'explorers',
            ],
        ];

        foreach ($data as $row) {
            $existing = null;

            if ($row['osm_type_code'] !== null) {
                $existing = $participantTypesTable->find()
                    ->where(['osm_type_code' => $row['osm_type_code']])
                    ->first();
            }

            if ($existing === null) {
                $existing = $participantTypesTable->find()
                    ->where(['participant_type' => $row['participant_type']])
                    ->first();
            }

            if ($existing === null) {
                $entity = $participantTypesTable->newEntity($row);
                $participantTypesTable->saveOrFail($entity);

                continue;
            }

            unset($row['created']);

            $entity = $participantTypesTable->patchEntity($existing, $row);
            $participantTypesTable->saveOrFail($entity);
        }
    }
}
