<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\View\Cell\LiveEventCheckpointsCell;
use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Events Model
 *
 * @property \App\Model\Table\CheckpointsTable $Checkpoints
 * @property \App\Model\Table\EntriesTable $Entries
 * @property \App\Model\Table\SectionsTable $Sections
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
        $this->addBehavior('Muffin/Trash.Trash');

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
        $this->hasMany('Questions', [
            'foreignKey' => 'event_id',
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

    /**
     * @param \Cake\Event\Event<static> $event
     * @param \Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        Cache::delete(LiveEventCheckpointsCell::CACHE_KEY, 'navigation');
    }

    /**
     * @param \Cake\Event\Event<static> $event
     * @param \Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        Cache::delete(LiveEventCheckpointsCell::CACHE_KEY, 'navigation');
    }
}
