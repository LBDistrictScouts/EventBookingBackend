<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EventsSections Model
 *
 * @property \App\Model\Table\SectionsTable&\Cake\ORM\Association\BelongsTo $Sections
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\BelongsTo $Events
 *
 * @method \App\Model\Entity\EventsSection newEmptyEntity()
 * @method \App\Model\Entity\EventsSection newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\EventsSection> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EventsSection get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\EventsSection findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\EventsSection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\EventsSection> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\EventsSection|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\EventsSection saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\EventsSection>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\EventsSection>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\EventsSection>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\EventsSection> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\EventsSection>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\EventsSection>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\EventsSection>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\EventsSection> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EventsSectionsTable extends Table
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

        $this->setTable('events_sections');
        $this->setDisplayField(['section_id', 'event_id']);
        $this->setPrimaryKey(['section_id', 'event_id']);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Events', [
            'foreignKey' => 'event_id',
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
        $rules->add($rules->existsIn(['section_id'], 'Sections'), ['errorField' => 'section_id']);
        $rules->add($rules->existsIn(['event_id'], 'Events'), ['errorField' => 'event_id']);

        return $rules;
    }
}
