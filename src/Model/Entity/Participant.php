<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Participant Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $entry_id
 * @property int $participant_type_id
 * @property int|null $section_id
 * @property bool $checked_in
 * @property bool $checked_out
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Entry $entry
 * @property \App\Model\Entity\ParticipantType $participant_type
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\CheckIn[] $check_ins
 */
class Participant extends Entity
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
        'first_name' => true,
        'last_name' => true,
        'entry_id' => true,
        'participant_type_id' => true,
        'section_id' => true,
        'checked_in' => true,
        'checked_out' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'entry' => true,
        'participant_type' => true,
        'section' => true,
        'check_ins' => true,
    ];
}
