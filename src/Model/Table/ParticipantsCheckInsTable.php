<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\ParticipantsCheckIn;
use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ParticipantsCheckIns Model
 *
 * @property \App\Model\Table\CheckInsTable&\Cake\ORM\Association\BelongsTo $CheckIns
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\BelongsTo $Participants
 * @method \App\Model\Entity\ParticipantsCheckIn newEmptyEntity()
 * @method \App\Model\Entity\ParticipantsCheckIn newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantsCheckIn> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ParticipantsCheckIn findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantsCheckIn> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ParticipantsCheckInsTable extends Table
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

        $this->setTable('participants_check_ins');
        $this->setDisplayField(['check_in_id', 'participant_id']);
        $this->setPrimaryKey(['check_in_id', 'participant_id']);

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', [
            'CheckIns' => [
                'participant_count',
            ],
        ]);

        $this->belongsTo('CheckIns', [
            'foreignKey' => 'check_in_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Participants', [
            'foreignKey' => 'participant_id',
            'joinType' => 'INNER',
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
            ->uuid('id')
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

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
        $rules->add($rules->existsIn(['check_in_id'], 'CheckIns'), ['errorField' => 'check_in_id']);
        $rules->add($rules->existsIn(['participant_id'], 'Participants'), ['errorField' => 'participant_id']);

        return $rules;
    }

    /**
     * @param string $participantId
     * @return void
     */
    public function refreshCounter(string $participantId): void
    {
        // Get the current max for this participant
        $currentAgg = $this->Participants->find()
            ->select([
                'max' => $this->CheckIns->Checkpoints->find()->func()->max('Checkpoints.checkpoint_sequence'),
                'min' => $this->CheckIns->Checkpoints->find()->func()->min('Checkpoints.checkpoint_sequence'),
            ])
            ->matching('CheckIns.Checkpoints')

            ->where(function (QueryExpression $exp, SelectQuery $q) {
                return $exp
                    ->gt('Checkpoints.checkpoint_sequence', -2);
            })
            ->where([
                'ParticipantsCheckIns.participant_id' => $participantId,
            ])
            ->firstOrFail();

        $currentMax = $currentAgg->max;

        if ($currentAgg->min < 0) {
            $checkpoints = $this->Participants->Entries->Events->find()
                ->select([
                    'count_checkpoints' =>
                        $this->CheckIns->Checkpoints->find()->func()->count('Checkpoints.id'),
                ])
                ->matching('Checkpoints')
                ->where(function (QueryExpression $exp, SelectQuery $q) {
                    return $exp
                        ->gt('Checkpoints.checkpoint_sequence', 0);
                })
                ->firstOrFail()->count_checkpoints;

            if ($checkpoints == $currentMax) {
                $currentMax += 1;
            }
        }

        // Update the participant record
        $this->Participants->updateAll(
            [
                'highest_check_in_sequence' => $currentMax,
                'checked_in' => true,
                'checked_out' => ($currentAgg->min < 0),
            ],
            [
                'id' => $participantId,
            ],
        );
    }

    /**
     * @param \Cake\Event\EventInterface $event
     * @param \App\Model\Entity\ParticipantsCheckIn $entity
     * @param \ArrayObject $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $this->refreshCounter($entity->get('participant_id'));
    }

    /**
     * @param \Cake\Event\EventInterface $event
     * @param \App\Model\Entity\ParticipantsCheckIn $entity
     * @param \ArrayObject $options
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $this->refreshCounter($entity->get('participant_id'));
    }
}
