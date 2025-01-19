<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Participants Model
 *
 * @property \App\Model\Table\EntriesTable&\Cake\ORM\Association\BelongsTo $Entries
 * @property \App\Model\Table\ParticipantTypesTable&\Cake\ORM\Association\BelongsTo $ParticipantTypes
 * @property \App\Model\Table\SectionsTable&\Cake\ORM\Association\BelongsTo $Sections
 * @property \App\Model\Table\CheckInsTable&\Cake\ORM\Association\BelongsToMany $CheckIns
 *
 * @method \App\Model\Entity\Participant newEmptyEntity()
 * @method \App\Model\Entity\Participant newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Participant> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Participant get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Participant findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Participant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Participant> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Participant|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Participant saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Participant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Participant>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Participant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Participant> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Participant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Participant>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Participant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Participant> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class ParticipantsTable extends Table
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

        $this->setTable('participants');
        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', [
            'Entries' => [
                'participant_count',
                'checked_in_count' => ['conditions' => ['Participants.checked_in' => true],],
            ],
            'Events' => [
                'participant_count',
                'checked_in_count' => ['conditions' => ['Participants.checked_in' => true],],
            ],
        ]);

        $this->belongsTo('Entries', [
            'foreignKey' => 'entry_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Events', [
            'through' => 'Entries',
            'cascadeCallbacks' => true,
        ]);
        $this->belongsTo('ParticipantTypes', [
            'foreignKey' => 'participant_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
        ]);
        $this->belongsToMany('CheckIns', [
            'foreignKey' => 'participant_id',
            'targetForeignKey' => 'check_in_id',
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
            ->scalar('first_name')
            ->maxLength('first_name', 255)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->uuid('entry_id')
            ->notEmptyString('entry_id');

        $validator
            ->uuid('participant_type_id')
            ->requirePresence('participant_type_id', 'create')
            ->notEmptyString('participant_type_id');

        $validator
            ->uuid('section_id')
            ->allowEmptyString('section_id');

        $validator
            ->boolean('checked_in')
            ->notEmptyString('checked_in');

        $validator
            ->boolean('checked_out')
            ->notEmptyString('checked_out');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        $validator
            ->integer('highest_check_in_sequence')
            ->notEmptyString('highest_check_in_sequence');

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
        $rules->add($rules->existsIn(['entry_id'], 'Entries'), ['errorField' => 'entry_id']);
        $rules->add($rules->existsIn(['participant_type_id'], 'ParticipantTypes'), ['errorField' => 'participant_type_id']);
        $rules->add($rules->existsIn(['section_id'], 'Sections'), ['errorField' => 'section_id']);

        return $rules;
    }
}
