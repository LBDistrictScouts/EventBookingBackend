<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ParticipantTypes Model
 *
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\HasMany $Participants
 * @property \App\Model\Table\SectionsTable&\Cake\ORM\Association\HasMany $Sections
 *
 * @method \App\Model\Entity\ParticipantType newEmptyEntity()
 * @method \App\Model\Entity\ParticipantType newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantType> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantType get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ParticipantType findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ParticipantType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantType> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantType|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ParticipantType saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantType>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantType>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantType>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantType> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantType>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantType>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantType>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantType> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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

        return $validator;
    }
}
