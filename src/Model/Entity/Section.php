<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Section Entity
 *
 * @property int $id
 * @property string $section_name
 * @property int $participant_type_id
 * @property int $group_id
 * @property int|null $osm_section_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\ParticipantType $participant_type
 * @property \App\Model\Entity\Group $group
 * @property \App\Model\Entity\Participant[] $participants
 * @property \App\Model\Entity\Event[] $events
 */
class Section extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'section_name' => true,
        'participant_type_id' => true,
        'group_id' => true,
        'osm_section_id' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'participant_type' => true,
        'group' => true,
        'participants' => true,
        'events' => true,
    ];
}
