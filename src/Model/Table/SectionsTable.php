<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sections Model
 *
 * @property \App\Model\Table\ParticipantTypesTable $ParticipantTypes
 * @property \App\Model\Table\GroupsTable $Groups
 * @property \App\Model\Table\ParticipantsTable $Participants
 * @property \App\Model\Table\EventsTable $Events
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
        $this->addBehavior('Muffin/Trash.Trash');

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
            ->scalar('notification_email')
            ->maxLength('notification_email', 255)
            ->allowEmptyString('notification_email')
            ->email('notification_email');

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
        $rules->add(
            $rules->isUnique(['osm_section_id'], ['allowMultipleNulls' => true]),
            ['errorField' => 'osm_section_id'],
        );
        $rules->add(
            $rules->existsIn(['participant_type_id'], 'ParticipantTypes'),
            ['errorField' => 'participant_type_id'],
        );
        $rules->add($rules->existsIn(['group_id'], 'Groups'), ['errorField' => 'group_id']);

        return $rules;
    }
}
