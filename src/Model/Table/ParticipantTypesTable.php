<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Enum\ParticipantTypeCategory;
use Cake\Database\Type\EnumType;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ParticipantTypes Model
 */
class ParticipantTypesTable extends Table
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

        $this->setTable('participant_types');
        $this->setDisplayField('participant_type');
        $this->setPrimaryKey('id');

        $this->getSchema()->setColumnType('category', EnumType::from(ParticipantTypeCategory::class));

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->hasMany('Participants', [
            'foreignKey' => 'participant_type_id',
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'participant_type_id',
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
            ->scalar('participant_type')
            ->maxLength('participant_type', 32)
            ->requirePresence('participant_type', 'create')
            ->notEmptyString('participant_type');

        $validator
            ->boolean('adult')
            ->requirePresence('adult', 'create')
            ->notEmptyString('adult');

        $validator
            ->boolean('uniformed')
            ->requirePresence('uniformed', 'create')
            ->notEmptyString('uniformed');

        $validator
            ->boolean('out_of_district')
            ->requirePresence('out_of_district', 'create')
            ->notEmptyString('out_of_district');

        $validator
            ->enum('category', ParticipantTypeCategory::class)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->integer('sort_order')
            ->requirePresence('sort_order', 'create')
            ->notEmptyString('sort_order');

        return $validator;
    }
}
