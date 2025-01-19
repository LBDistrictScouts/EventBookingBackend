<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entry Entity
 *
 * @property string $id
 * @property string $event_id
 * @property string $entry_name
 * @property bool $active
 * @property int $participant_count
 * @property int $checked_in_count
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 * @property string $entry_email
 *
 * @property \App\Model\Entity\Event $event
 * @property \App\Model\Entity\CheckIn[] $check_ins
 * @property \App\Model\Entity\Participant[] $participants
 */
class Entry extends Entity
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
        'event_id' => true,
        'entry_name' => true,
        'active' => true,
        'participant_count' => true,
        'checked_in_count' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'entry_email' => true,
        'event' => true,
        'check_ins' => true,
        'participants' => true,
    ];
}
