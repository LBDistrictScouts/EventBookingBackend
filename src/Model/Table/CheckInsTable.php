<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CheckIns Model
 *
 * @property \App\Model\Table\CheckpointsTable $Checkpoints
 * @property \App\Model\Table\EntriesTable $Entries
 * @property \App\Model\Table\ParticipantsTable $Participants
 * @property \App\Model\Table\ParticipantsCheckInsTable $ParticipantsCheckIns
 */
class CheckInsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('check_ins');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('Checkpoints', [
            'foreignKey' => 'checkpoint_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Entries', [
            'foreignKey' => 'entry_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Participants', [
            'foreignKey' => 'check_in_id',
            'targetForeignKey' => 'participant_id',
            'joinTable' => 'participants_check_ins',
            'through' => 'ParticipantsCheckIns',
        ]);
        $this->hasMany('ParticipantsCheckIns', [
            'foreignKey' => 'check_in_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('checkpoint_id')
            ->requirePresence('checkpoint_id', 'create')
            ->notEmptyString('checkpoint_id');

        $validator
            ->uuid('entry_id')
            ->requirePresence('entry_id', 'create')
            ->notEmptyString('entry_id');

        $validator
            ->dateTime('check_in_time')
            ->requirePresence('check_in_time', 'create')
            ->notEmptyDateTime('check_in_time');

        $validator
            ->integer('participant_count')
            ->notEmptyString('participant_count');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['checkpoint_id'], 'Checkpoints'), ['errorField' => 'checkpoint_id']);
        $rules->add($rules->existsIn(['entry_id'], 'Entries'), ['errorField' => 'entry_id']);

        return $rules;
    }

    /**
     * @param \Cake\Event\EventInterface<static> $event
     * @param \App\Model\Entity\ParticipantsCheckIn $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $participants = $this->Participants->find()->matching('CheckIns');

        foreach ($participants as $participant) {
            if (!$participant instanceof EntityInterface) {
                continue;
            }

            $participantId = $participant->get('id');
            if (is_string($participantId)) {
                $this->ParticipantsCheckIns->refreshCounter($participantId);
            }
        }
    }
}
