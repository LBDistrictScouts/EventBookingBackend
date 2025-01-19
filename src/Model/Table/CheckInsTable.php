<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CheckIns Model
 *
 * @property \App\Model\Table\CheckpointsTable&\Cake\ORM\Association\BelongsTo $Checkpoints
 * @property \App\Model\Table\EntriesTable&\Cake\ORM\Association\BelongsTo $Entries
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\BelongsToMany $Participants
 *
 * @method \App\Model\Entity\CheckIn newEmptyEntity()
 * @method \App\Model\Entity\CheckIn newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CheckIn> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CheckIn get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CheckIn findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CheckIn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CheckIn> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CheckIn|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CheckIn saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CheckIn>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CheckIn> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CheckIn>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CheckIn> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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
            ->notEmptyString('checkpoint_id');

        $validator
            ->uuid('entry_id')
            ->notEmptyString('entry_id');

        $validator
            ->dateTime('check_in_time')
            ->requirePresence('check_in_time', 'create')
            ->notEmptyDateTime('check_in_time');

        $validator
            ->integer('participant_count')
            ->requirePresence('participant_count', 'create')
            ->notEmptyString('participant_count');

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
        $rules->add($rules->existsIn(['checkpoint_id'], 'Checkpoints'), ['errorField' => 'checkpoint_id']);
        $rules->add($rules->existsIn(['entry_id'], 'Entries'), ['errorField' => 'entry_id']);

        return $rules;
    }
}
