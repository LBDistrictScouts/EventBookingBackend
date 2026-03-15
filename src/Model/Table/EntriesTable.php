<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Entry;
use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Entries Model
 *
 * @property \App\Model\Table\EventsTable $Events
 * @property \App\Model\Table\CheckInsTable $CheckIns
 * @property \App\Model\Table\ParticipantsTable $Participants
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
     * @param \Cake\Event\EventInterface<static> $event
     * @param \App\Model\Entity\Entry $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function beforeSave(EventInterface $event, Entry $entity, ArrayObject $options): void
    {
        if ($entity->isNew() && isset($entity->event_id)) {
            // Get the current max reference number for this event
            $maxRef = $this->find()
                ->find('withTrashed')
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

    /**
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Entry> $query
     * @param bool $public
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Entry>
     */
    public function findApiSignature(SelectQuery $query, bool $public = true): SelectQuery
    {
        $query->contain([
            'Events' => ['Checkpoints', 'Questions'],
            'CheckIns',
            'Participants' => ['ParticipantTypes', 'Sections'],
        ]);

        if (!$public) {
            return $query;
        }

        return $query->formatResults(
            function (CollectionInterface $results): CollectionInterface {
                return $results->map(
                    fn(Entry $entry): Entry => $entry->hidePublicFields(),
                );
            },
        );
    }

    /**
     * @param string $entryId
     * @param bool $public
     * @return \App\Model\Entity\Entry
     */
    public function getApiEntryById(string $entryId, bool $public = true): Entry
    {
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $this->find('apiSignature', public: $public)
            ->where([$this->aliasField('id') => $entryId])
            ->firstOrFail();

        return $entry;
    }

    /**
     * @param int $referenceNumber
     * @param string $securityCode
     * @param bool $public
     * @return \App\Model\Entity\Entry
     */
    public function getApiEntryByLookup(
        int $referenceNumber,
        string $securityCode,
        bool $public = true,
    ): Entry {
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $this->find('apiSignature', public: $public)
            ->where([
                'reference_number' => $referenceNumber,
                'security_code' => $securityCode,
            ])
            ->firstOrFail();

        return $entry;
    }

    /**
     * Merge one entry into another by moving participants and deleting the merged entry.
     *
     * @param string $persistingEntryId
     * @param string $mergingEntryId
     * @return int|false
     */
    public function mergeEntries(string $persistingEntryId, string $mergingEntryId): int|false
    {
        $participants = $this->Participants->find()
            ->where(['entry_id' => $mergingEntryId])
            ->all();

        foreach ($participants as $participant) {
            if (!$participant instanceof EntityInterface) {
                continue;
            }

            $participant->set('entry_id', $persistingEntryId);
            $this->Participants->save($participant);

            if ($participant->getErrors()) {
                return false;
            }
        }

        $participantCount = $this->Participants->find()
            ->where(['entry_id' => $mergingEntryId])
            ->all()
            ->count();

        if ($participantCount === 0) {
            $mergingEntry = $this->get($mergingEntryId);
            $this->delete($mergingEntry);

            return $this->Participants->find()
                ->where(['entry_id' => $persistingEntryId])
                ->all()
                ->count();
        }

        return false;
    }
}
