<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EventsSection Entity
 *
 * @property string $section_id
 * @property string $event_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\Event $event
 */
class EventsSection extends Entity
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
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'section' => true,
        'event' => true,
    ];
}
