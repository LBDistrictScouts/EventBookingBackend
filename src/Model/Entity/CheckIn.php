<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CheckIn Entity
 *
 * @property int $id
 * @property int $checkpoint_id
 * @property int $entry_id
 * @property \Cake\I18n\DateTime $check_in_time
 * @property int $participant_count
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Checkpoint $checkpoint
 * @property \App\Model\Entity\Entry $entry
 * @property \App\Model\Entity\Participant[] $participants
 */
class CheckIn extends Entity
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
        'checkpoint_id' => true,
        'entry_id' => true,
        'check_in_time' => true,
        'participant_count' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'checkpoint' => true,
        'entry' => true,
        'participants' => true,
    ];
}
