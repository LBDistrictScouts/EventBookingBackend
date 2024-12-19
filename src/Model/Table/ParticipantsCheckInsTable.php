<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ParticipantsCheckIns Model
 *
 * @property \App\Model\Table\CheckInsTable&\Cake\ORM\Association\BelongsTo $CheckIns
 * @property \App\Model\Table\ParticipantsTable&\Cake\ORM\Association\BelongsTo $Participants
 *
 * @method \App\Model\Entity\ParticipantsCheckIn newEmptyEntity()
 * @method \App\Model\Entity\ParticipantsCheckIn newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantsCheckIn> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ParticipantsCheckIn findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ParticipantsCheckIn> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ParticipantsCheckIn saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ParticipantsCheckIn>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ParticipantsCheckIn> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ParticipantsCheckInsTable extends Table
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

        $this->setTable('participants_check_ins');
        $this->setDisplayField(['check_in_id', 'participant_id']);
        $this->setPrimaryKey(['check_in_id', 'participant_id']);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('CheckIns', [
            'foreignKey' => 'check_in_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Participants', [
            'foreignKey' => 'participant_id',
            'joinType' => 'INNER',
        ]);
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
        $rules->add($rules->existsIn(['check_in_id'], 'CheckIns'), ['errorField' => 'check_in_id']);
        $rules->add($rules->existsIn(['participant_id'], 'Participants'), ['errorField' => 'participant_id']);

        return $rules;
    }
}
