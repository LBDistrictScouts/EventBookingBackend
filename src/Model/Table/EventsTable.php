<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Events Model
 *
 * @property \App\Model\Table\CheckpointsTable&\Cake\ORM\Association\HasMany $Checkpoints
 * @property \App\Model\Table\EntriesTable&\Cake\ORM\Association\HasMany $Entries
 * @property \App\Model\Table\SectionsTable&\Cake\ORM\Association\BelongsToMany $Sections
 *
 * @method \App\Model\Entity\Event newEmptyEntity()
 * @method \App\Model\Entity\Event newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Event> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Event get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Event findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Event patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Event> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Event|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Event saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Event>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Event>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Event>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Event> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Event>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Event>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Event>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Event> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EventsTable extends Table
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

        $this->setTable('events');
        $this->setDisplayField('event_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Checkpoints', [
            'foreignKey' => 'event_id',
        ]);
        $this->hasMany('Entries', [
            'foreignKey' => 'event_id',
        ]);
        $this->belongsToMany('Sections', [
            'foreignKey' => 'event_id',
            'targetForeignKey' => 'section_id',
            'joinTable' => 'events_sections',
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
            ->scalar('event_name')
            ->maxLength('event_name', 64)
            ->requirePresence('event_name', 'create')
            ->notEmptyString('event_name')
            ->add('event_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('event_description')
            ->maxLength('event_description', 255)
            ->requirePresence('event_description', 'create')
            ->notEmptyString('event_description');

        $validator
            ->scalar('booking_code')
            ->maxLength('booking_code', 20)
            ->requirePresence('booking_code', 'create')
            ->notEmptyString('booking_code')
            ->add('booking_code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->dateTime('start_time')
            ->requirePresence('start_time', 'create')
            ->notEmptyDateTime('start_time');

        $validator
            ->boolean('bookable')
            ->notEmptyString('bookable');

        $validator
            ->boolean('finished')
            ->notEmptyString('finished');

        $validator
            ->integer('entry_count')
            ->notEmptyString('entry_count');

        $validator
            ->integer('participant_count')
            ->notEmptyString('participant_count');

        $validator
            ->integer('checked_in_count')
            ->notEmptyString('checked_in_count');

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
        $rules->add($rules->isUnique(['event_name']), ['errorField' => 'event_name']);
        $rules->add($rules->isUnique(['booking_code']), ['errorField' => 'booking_code']);

        return $rules;
    }
}
