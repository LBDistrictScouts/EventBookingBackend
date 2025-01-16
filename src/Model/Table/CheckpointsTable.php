<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Checkpoints Model
 *
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\BelongsTo $Events
 * @property \App\Model\Table\CheckInsTable&\Cake\ORM\Association\HasMany $CheckIns
 *
 * @method \App\Model\Entity\Checkpoint newEmptyEntity()
 * @method \App\Model\Entity\Checkpoint newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Checkpoint> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Checkpoint get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Checkpoint findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Checkpoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Checkpoint> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Checkpoint|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Checkpoint saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Checkpoint>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Checkpoint>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Checkpoint>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Checkpoint> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Checkpoint>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Checkpoint>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Checkpoint>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Checkpoint> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CheckpointsTable extends Table
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

        $this->setTable('checkpoints');
        $this->setDisplayField('checkpoint_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash', ['field' => 'deleted']);

        $this->belongsTo('Events', [
            'foreignKey' => 'event_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CheckIns', [
            'foreignKey' => 'checkpoint_id',
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
            ->integer('checkpoint_sequence')
            ->requirePresence('checkpoint_sequence', 'create')
            ->notEmptyString('checkpoint_sequence');

        $validator
            ->scalar('checkpoint_name')
            ->maxLength('checkpoint_name', 255)
            ->requirePresence('checkpoint_name', 'create')
            ->notEmptyString('checkpoint_name');

        $validator
            ->integer('event_id')
            ->notEmptyString('event_id');

        $validator
            ->uuid('external_id')
            ->add('external_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['checkpoint_sequence', 'event_id']), ['errorField' => 'checkpoint_sequence']);
        $rules->add($rules->isUnique(['external_id']), ['errorField' => 'external_id']);
        $rules->add($rules->existsIn(['event_id'], 'Events'), ['errorField' => 'event_id']);

        return $rules;
    }
}
