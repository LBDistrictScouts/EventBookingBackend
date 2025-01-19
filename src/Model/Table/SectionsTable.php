<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sections Model
 *
 * @property \App\Model\Table\ParticipantTypesTable&\Cake\ORM\Association\BelongsTo $ParticipantTypes
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\HasMany $Participants
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\BelongsToMany $Events
 *
 * @method \App\Model\Entity\Section newEmptyEntity()
 * @method \App\Model\Entity\Section newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Section> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Section get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Section findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Section patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Section> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Section|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Section saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Section>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Section>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Section>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Section> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Section>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Section>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Section>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Section> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SectionsTable extends Table
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

        $this->setTable('sections');
        $this->setDisplayField('section_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ParticipantTypes', [
            'foreignKey' => 'participant_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Participants', [
            'foreignKey' => 'section_id',
        ]);
        $this->belongsToMany('Events', [
            'foreignKey' => 'section_id',
            'targetForeignKey' => 'event_id',
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
            ->scalar('section_name')
            ->maxLength('section_name', 255)
            ->requirePresence('section_name', 'create')
            ->notEmptyString('section_name');

        $validator
            ->uuid('participant_type_id')
            ->notEmptyString('participant_type_id');

        $validator
            ->uuid('group_id')
            ->notEmptyString('group_id');

        $validator
            ->integer('osm_section_id')
            ->allowEmptyString('osm_section_id')
            ->add('osm_section_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['osm_section_id'], ['allowMultipleNulls' => true]), ['errorField' => 'osm_section_id']);
        $rules->add($rules->existsIn(['participant_type_id'], 'ParticipantTypes'), ['errorField' => 'participant_type_id']);
        $rules->add($rules->existsIn(['group_id'], 'Groups'), ['errorField' => 'group_id']);

        return $rules;
    }
}
