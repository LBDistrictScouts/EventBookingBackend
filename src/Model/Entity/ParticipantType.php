<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ParticipantType Entity
 *
 * @property string $id
 * @property string $participant_type
 * @property bool $adult
 * @property bool $uniformed
 * @property bool $out_of_district
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \App\Model\Enum\ParticipantTypeCategory|null $category
 *
 * @property \App\Model\Entity\Participant[] $participants
 * @property \App\Model\Entity\Section[] $sections
 */
class ParticipantType extends Entity
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
        'participant_type' => true,
        'adult' => true,
        'uniformed' => true,
        'out_of_district' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'category' => true,
        'participants' => true,
        'sections' => true,
    ];
}
