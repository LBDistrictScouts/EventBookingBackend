<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ParticipantsCheckIn Entity
 *
 * @property string $id
 * @property string $check_in_id
 * @property string $participant_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\CheckIn $check_in
 * @property \App\Model\Entity\Participant $participant
 */
class ParticipantsCheckIn extends Entity
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
        'id' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'check_in' => true,
        'participant' => true,
    ];
}
