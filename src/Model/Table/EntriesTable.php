<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Entry;
use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Entries Model
 *
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\BelongsTo $Events
 * @property \App\Model\Table\CheckInsTable&\Cake\ORM\Association\HasMany $CheckIns
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\HasMany $Participants
 * @method \App\Model\Entity\Entry newEmptyEntity()
 * @method \App\Model\Entity\Entry newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Entry> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Entry get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Entry findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Entry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Entry> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Entry|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Entry saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Entry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Entry>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Entry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Entry> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Entry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Entry>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Entry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Entry> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class EntriesTable extends Table
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

        $this->setTable('entries');
        $this->setDisplayField('entry_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('CounterCache', [
            'Events' => ['entry_count'],
        ]);

        $this->belongsTo('Events', [
            'foreignKey' => 'event_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CheckIns', [
            'foreignKey' => 'entry_id',
        ]);
        $this->hasMany('Participants', [
            'foreignKey' => 'entry_id',
        ]);
    }

    /**
     * @param \Cake\Event\EventInterface $event
     * @param \App\Model\Entity\Entry $entity
     * @param \ArrayObject $options
     * @return void
     */
    public function beforeSave(EventInterface $event, Entry $entity, ArrayObject $options): void
    {
        if ($entity->isNew() && isset($entity->event_id)) {
            // Get the current max reference number for this event
            $maxRef = $this->find()
                ->select(['max_ref' => $this->find()->func()->max('reference_number')])
                ->where(['event_id' => $entity->event_id])
                ->first()
                ->max_ref ?? 0;

            $entity->reference_number = $maxRef + 1;
        }
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
            ->uuid('event_id')
            ->notEmptyString('event_id');

        $validator
            ->scalar('entry_name')
            ->maxLength('entry_name', 32)
            ->requirePresence('entry_name', 'create')
            ->notEmptyString('entry_name');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->integer('participant_count')
            ->notEmptyString('participant_count');

        $validator
            ->integer('checked_in_count')
            ->notEmptyString('checked_in_count');

        $validator
            ->scalar('entry_email')
            ->email('entry_email')
            ->maxLength('entry_email', 255)
            ->requirePresence('entry_email', 'create')
            ->notEmptyString('entry_email');

        $validator
            ->scalar('entry_mobile')
            ->requirePresence('entry_mobile', 'create')
            ->maxLength('entry_mobile', 20)
            ->allowEmptyString('entry_mobile');

        $validator
            ->scalar('security_code')
            ->maxLength('security_code', 5)
            ->allowEmptyString('security_code');

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
        $rules->add($rules->isUnique(['event_id', 'entry_name']), ['errorField' => 'entry_name']);
        $rules->add($rules->existsIn(['event_id'], 'Events'), ['errorField' => 'event_id']);

        return $rules;
    }
}
