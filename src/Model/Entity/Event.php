<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property string $id
 * @property string $event_name
 * @property string $event_description
 * @property string $booking_code
 * @property \Cake\I18n\DateTime $start_time
 * @property bool $bookable
 * @property bool $finished
 * @property int $entry_count
 * @property int $participant_count
 * @property int $checked_in_count
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Checkpoint[] $checkpoints
 * @property \App\Model\Entity\Entry[] $entries
 * @property \App\Model\Entity\Section[] $sections
 */
class Event extends Entity
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
        'event_name' => true,
        'event_description' => true,
        'booking_code' => true,
        'start_time' => true,
        'bookable' => true,
        'finished' => true,
        'entry_count' => true,
        'participant_count' => true,
        'checked_in_count' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'checkpoints' => true,
        'entries' => true,
        'sections' => true,
    ];
}
