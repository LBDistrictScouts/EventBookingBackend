<?php
declare(strict_types=1);

use App\Model\Enum\ParticipantTypeCategory;
use Cake\Core\Configure;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Table;
use Cake\Utility\Text;
use LBDistrictScouts\CoreDataSeeder\Service\CoreDataService;
use Migrations\BaseSeed;

/**
 * Imports section reference data from District Core Data.
 *
 * The seed is idempotent and only creates missing groups, participant types,
 * and sections. Participant types are merged primarily via `osm_type_code`.
 *
 * @phpstan-type SectionTypeDefaults array{
 *     participant_type: string,
 *     adult: bool,
 *     uniformed: bool,
 *     out_of_district: bool,
 *     category: \App\Model\Enum\ParticipantTypeCategory,
 *     sort_order: int
 * }
 * @phpstan-type NormalizedSection array{
 *     section_name: string,
 *     osm_section_id: int|null,
 *     group_name: string,
 *     group_sort_order: int,
 *     participant_osm_type_code: string,
 *     participant_type: string,
 *     participant_sort_order: int,
 *     participant_adult: bool,
 *     participant_uniformed: bool,
 *     participant_out_of_district: bool,
 *     participant_category: \App\Model\Enum\ParticipantTypeCategory
 * }
 * @phpstan-type CreatedCounts array{
 *     groups: int,
 *     participantTypes: int,
 *     sections: int,
 *     skipped: int
 * }
 */
class CoreDataSeederSeed extends BaseSeed
{
    /**
     * Default local participant-type values keyed by Core Data `section_type`.
     *
     * @var array<string, SectionTypeDefaults>
     */
    private const array SECTION_TYPE_MAP = [
        'earlyyears' => [
            'participant_type' => 'Squirrels',
            'adult' => false,
            'uniformed' => false,
            'out_of_district' => false,
            'category' => ParticipantTypeCategory::YoungPerson,
            'sort_order' => 0,
        ],
        'beavers' => [
            'participant_type' => 'Beavers',
            'adult' => false,
            'uniformed' => true,
            'out_of_district' => false,
            'category' => ParticipantTypeCategory::YoungPerson,
            'sort_order' => 1,
        ],
        'cubs' => [
            'participant_type' => 'Cubs',
            'adult' => false,
            'uniformed' => true,
            'out_of_district' => false,
            'category' => ParticipantTypeCategory::YoungPerson,
            'sort_order' => 2,
        ],
        'scouts' => [
            'participant_type' => 'Scouts',
            'adult' => false,
            'uniformed' => true,
            'out_of_district' => false,
            'category' => ParticipantTypeCategory::YoungPerson,
            'sort_order' => 3,
        ],
        'explorers' => [
            'participant_type' => 'Explorers',
            'adult' => false,
            'uniformed' => true,
            'out_of_district' => false,
            'category' => ParticipantTypeCategory::YoungPerson,
            'sort_order' => 4,
        ],
    ];

    /**
     * Seeds can be safely re-run to pick up new Core Data records.
     *
     * @return bool
     */
    public function isIdempotent(): bool
    {
        return true;
    }

    /**
     * Import the latest section reference data from District Core Data.
     *
     * @return void
     */
    public function run(): void
    {
        $sectionsPayload = $this->buildCoreDataService()->getSections();
        $sections = $this->extractSections($sectionsPayload);

        /** @var \Cake\ORM\Table $groupsTable */
        $groupsTable = FactoryLocator::get('Table')->get('Groups');
        /** @var \Cake\ORM\Table $participantTypesTable */
        $participantTypesTable = FactoryLocator::get('Table')->get('ParticipantTypes');
        /** @var \Cake\ORM\Table $sectionsTable */
        $sectionsTable = FactoryLocator::get('Table')->get('Sections');

        /** @var CreatedCounts $createdCounts */
        $createdCounts = [
            'groups' => 0,
            'participantTypes' => 0,
            'sections' => 0,
            'skipped' => 0,
        ];

        foreach ($sections as $index => $rawSection) {
            $section = $this->normalizeSection($rawSection, $index + 1);
            if ($section === null) {
                $createdCounts['skipped']++;
                continue;
            }

            $group = $this->findOrCreateGroup($groupsTable, $section, $createdCounts);
            $participantType = $this->findOrCreateParticipantType($participantTypesTable, $section, $createdCounts);
            $existingSection = $this->findExistingSection(
                $sectionsTable,
                $section,
                (string)$group->id,
                (string)$participantType->id,
            );
            if ($existingSection !== null) {
                continue;
            }

            $entity = $sectionsTable->newEntity([
                'id' => Text::uuid(),
                'section_name' => $section['section_name'],
                'participant_type_id' => $participantType->id,
                'group_id' => $group->id,
                'osm_section_id' => $section['osm_section_id'],
            ]);
            $sectionsTable->saveOrFail($entity);
            $createdCounts['sections']++;
        }

        $this->getIo()?->out(sprintf(
            'CoreDataSeederSeed complete: %d groups, %d participant types, %d sections created, %d rows skipped.',
            $createdCounts['groups'],
            $createdCounts['participantTypes'],
            $createdCounts['sections'],
            $createdCounts['skipped'],
        ));
    }

    /**
     * Build a Core Data client from application configuration.
     *
     * @return \LBDistrictScouts\CoreDataSeeder\Service\CoreDataService
     */
    private function buildCoreDataService(): CoreDataService
    {
        return new CoreDataService(
            $this->readRequiredConfig('CoreDataSeeder.url'),
            $this->readRequiredConfig('CoreDataSeeder.username'),
            $this->readRequiredConfig('CoreDataSeeder.password'),
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return list<array<string, mixed>>
     */
    private function extractSections(array $payload): array
    {
        if (isset($payload['sections']) && is_array($payload['sections'])) {
            /** @var list<array<string, mixed>> $sections */
            $sections = array_values(array_filter($payload['sections'], 'is_array'));

            return $sections;
        }

        if (array_is_list($payload)) {
            /** @var list<array<string, mixed>> $sections */
            $sections = array_values(array_filter($payload, 'is_array'));

            return $sections;
        }

        throw new RuntimeException('Core Data sections payload was not a list and did not contain a `sections` key.');
    }

    /**
     * @param array<string, mixed> $rawSection
     * @param int $position One-based position within the source payload.
     * @return NormalizedSection|null
     */
    private function normalizeSection(array $rawSection, int $position): ?array
    {
        $groupName = $this->readRequiredString($rawSection, 'group');
        $sectionName = $this->readRequiredString($rawSection, 'section_name');
        $sectionType = $this->readRequiredString($rawSection, 'section_type');
        $mapping = self::SECTION_TYPE_MAP[strtolower($sectionType)] ?? null;

        if ($groupName === null || $sectionName === null || $mapping === null) {
            $this->getIo()?->warning(sprintf(
                'Skipping Core Data section row %d because required Core Data fields were missing
                or the section type is unsupported.',
                $position,
            ));

            return null;
        }

        return [
            'section_name' => $sectionName,
            'osm_section_id' => $this->readInt($rawSection, ['section_id']),
            'group_name' => $groupName,
            'group_sort_order' => $position,
            'participant_osm_type_code' => strtolower($sectionType),
            'participant_type' => $mapping['participant_type'],
            'participant_sort_order' => $mapping['sort_order'],
            'participant_adult' => $mapping['adult'],
            'participant_uniformed' => $mapping['uniformed'],
            'participant_out_of_district' => $mapping['out_of_district'],
            'participant_category' => $mapping['category'],
        ];
    }

    /**
     * Find an existing group by name or create it when missing.
     *
     * @param \Cake\ORM\Table $groupsTable The groups table.
     * @param NormalizedSection $section The normalised section row.
     * @param CreatedCounts $createdCounts Mutable counters for reporting.
     * @return object
     */
    private function findOrCreateGroup(Table $groupsTable, array $section, array &$createdCounts): object
    {
        $group = $groupsTable->find()
            ->where(['group_name' => $section['group_name']])
            ->first();
        if ($group !== null) {
            return $group;
        }

        $group = $groupsTable->newEntity([
            'id' => Text::uuid(),
            'group_name' => $section['group_name'],
            'visible' => true,
            'sort_order' => $section['group_sort_order'],
        ]);
        $groupsTable->saveOrFail($group);
        $createdCounts['groups']++;

        return $group;
    }

    /**
     * Find an existing participant type by `osm_type_code` or create it.
     *
     * If a legacy participant type is found by display name only, its
     * `osm_type_code` is backfilled to stabilise future merges.
     *
     * @param \Cake\ORM\Table $participantTypesTable The participant types table.
     * @param NormalizedSection $section The normalised section row.
     * @param CreatedCounts $createdCounts Mutable counters for reporting.
     * @return object
     */
    private function findOrCreateParticipantType(
        Table $participantTypesTable,
        array $section,
        array &$createdCounts,
    ): object {
        $participantType = $participantTypesTable->find()
            ->where(['osm_type_code' => $section['participant_osm_type_code']])
            ->first();
        if ($participantType !== null) {
            return $participantType;
        }

        $participantType = $participantTypesTable->find()
            ->where(['participant_type' => $section['participant_type']])
            ->first();
        if ($participantType !== null) {
            if (($participantType->osm_type_code ?? null) !== $section['participant_osm_type_code']) {
                $participantType = $participantTypesTable->patchEntity($participantType, [
                    'osm_type_code' => $section['participant_osm_type_code'],
                ]);
                $participantTypesTable->saveOrFail($participantType);
            }

            return $participantType;
        }

        $participantType = $participantTypesTable->newEntity([
            'id' => Text::uuid(),
            'osm_type_code' => $section['participant_osm_type_code'],
            'participant_type' => $section['participant_type'],
            'adult' => $section['participant_adult'],
            'uniformed' => $section['participant_uniformed'],
            'out_of_district' => $section['participant_out_of_district'],
            'category' => $section['participant_category'],
            'sort_order' => $section['participant_sort_order'],
        ]);
        $participantTypesTable->saveOrFail($participantType);
        $createdCounts['participantTypes']++;

        return $participantType;
    }

    /**
     * Locate an existing section using OSM section ID or the local composite key.
     *
     * @param \Cake\ORM\Table $sectionsTable The sections table.
     * @param NormalizedSection $section The normalised section row.
     * @param string $groupId The resolved local group UUID.
     * @param string $participantTypeId The resolved local participant type UUID.
     * @return object|null
     */
    private function findExistingSection(
        Table $sectionsTable,
        array $section,
        string $groupId,
        string $participantTypeId,
    ): ?object {
        if ($section['osm_section_id'] !== null) {
            $existingSection = $sectionsTable->find()
                ->where(['osm_section_id' => $section['osm_section_id']])
                ->first();
            if ($existingSection !== null) {
                return $existingSection;
            }
        }

        return $sectionsTable->find()
            ->where([
                'section_name' => $section['section_name'],
                'group_id' => $groupId,
                'participant_type_id' => $participantTypeId,
            ])
            ->first();
    }

    /**
     * Read a required string field from a raw Core Data row.
     *
     * @param array<string, mixed> $data The raw Core Data row.
     * @param string $key The field name to read.
     * @return string|null
     */
    private function readRequiredString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string> $keys
     * @param list<string> $nestedContainers
     */
    private function readInt(array $data, array $keys, array $nestedContainers = []): ?int
    {
        foreach ($keys as $key) {
            $value = $data[$key] ?? null;
            if (is_int($value)) {
                return $value;
            }
            if (is_string($value) && $value !== '' && is_numeric($value)) {
                return (int)$value;
            }
        }

        foreach ($nestedContainers as $container) {
            $nested = $data[$container] ?? null;
            if (!is_array($nested)) {
                continue;
            }

            foreach ($keys as $key) {
                $value = $nested[$key] ?? null;
                if (is_int($value)) {
                    return $value;
                }
                if (is_string($value) && $value !== '' && is_numeric($value)) {
                    return (int)$value;
                }
            }
        }

        return null;
    }

    /**
     * Read a required application configuration value.
     *
     * @param string $path The Cake Configure path.
     * @return string
     */
    private function readRequiredConfig(string $path): string
    {
        $value = Configure::read($path);
        if (!is_string($value) || $value === '') {
            throw new RuntimeException(sprintf('Missing required configuration `%s`.', $path));
        }

        return $value;
    }
}
