<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Checkpoint Entity
 *
 * @property int $id
 * @property int $checkpoint_sequence
 * @property string $checkpoint_name
 * @property int $event_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Event $event
 * @property \App\Model\Entity\CheckIn[] $check_ins
 */
class Checkpoint extends Entity
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
        'checkpoint_sequence' => true,
        'checkpoint_name' => true,
        'event_id' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'event' => true,
        'check_ins' => true,
    ];
}
