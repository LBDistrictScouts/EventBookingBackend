<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * EventsSections Model
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
