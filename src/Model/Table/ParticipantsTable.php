<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Participant;
use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Participants Model
 *
 * @property \App\Model\Table\EntriesTable $Entries
 * @property \App\Model\Table\EventsTable $Events
 * @property \App\Model\Table\ParticipantTypesTable $ParticipantTypes
 * @property \App\Model\Table\SectionsTable $Sections
 * @property \App\Model\Table\CheckInsTable $CheckIns
 */
class ParticipantsTable extends Table
{
    use locatorAwareTrait;

    protected const FINISH_SEQUENCE = -1;
    protected const SURVEY_SEQUENCE = -2;

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
        $this->setDisplayField('full_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('CounterCache', [
            'Entries' => [
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
            'through' => 'ParticipantsCheckIns',
        ]);
    }

    /**
     * @param \Cake\Event\Event<static> $event
     * @param \App\Model\Entity\Participant $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function afterSave(Event $event, Participant $entity, ArrayObject $options): void
    {
        if (!$entity->isNew()) {
            return;
        }

        $this->updateEventParticipantCount($entity->entry_id);
    }

    /**
     * @param \Cake\Event\Event<static> $event
     * @param \App\Model\Entity\Participant $entity
     * @param \ArrayObject<string, mixed> $options
     * @return void
     */
    public function afterDelete(Event $event, Participant $entity, ArrayObject $options): void
    {
        $this->updateEventParticipantCount($entity->entry_id);
    }

    /**
     * Function to generate counts of participants
     *
     * @param string $entryId
     * @return void
     */
    protected function updateEventParticipantCount(string $entryId): void
    {
        $entriesTable = $this->getTableLocator()->get('Entries');
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entriesTable->get($entryId, contain: ['Events']);

        $participantCount = $this->find()
            ->matching('Entries', function ($q) use ($entry) {
                return $q->where(['Entries.event_id' => $entry->event_id]);
            })
            ->count();

        $eventsTable = $this->getTableLocator()->get('Events');
        $eventsTable->updateAll(['participant_count' => $participantCount], ['id' => $entry->event_id]);
    }

    /**
     * Recalculate participant-related counter caches for an entry and its event.
     *
     * @param string $entryId
     * @return void
     */
    public function refreshCounterCaches(string $entryId): void
    {
        $entriesTable = $this->getTableLocator()->get('Entries');

        $participantCount = $this->find()
            ->where(['Participants.entry_id' => $entryId])
            ->count();
        $checkedInCount = $this->find()
            ->where([
                'Participants.entry_id' => $entryId,
                'Participants.checked_in' => true,
            ])
            ->count();

        $entriesTable->updateAll(
            [
                'participant_count' => $participantCount,
                'checked_in_count' => $checkedInCount,
            ],
            ['id' => $entryId],
        );

        $this->updateEventParticipantCount($entryId);
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
            ->uuid('access_key')
            ->allowEmptyString('access_key');

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
        $rules->add(
            $rules->existsIn(['participant_type_id'], 'ParticipantTypes'),
            ['errorField' => 'participant_type_id'],
        );
        $rules->add($rules->existsIn(['section_id'], 'Sections'), ['errorField' => 'section_id']);

        return $rules;
    }

    /**
     * Find participants whose highest recorded checkpoint sequence is before a target sequence.
     *
     * Optional filters allow narrowing to an event or entry, excluding participants already
     * checked into a given checkpoint, and excluding checked-out participants.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findBeforeSequence(SelectQuery $query, array $options): SelectQuery
    {
        $sequence = (int)($options['sequence'] ?? 0);
        $minimumSequence = $options['minimumSequence'] ?? null;
        $eventId = $options['eventId'] ?? null;
        $entryId = $options['entryId'] ?? null;
        $excludeCheckpointId = $options['excludeCheckpointId'] ?? null;
        $excludeCheckedOut = array_key_exists('excludeCheckedOut', $options)
            ? (bool)$options['excludeCheckedOut']
            : $sequence !== self::SURVEY_SEQUENCE;

        $this->applySequenceEligibility($query, $sequence);
        if ($sequence === self::SURVEY_SEQUENCE) {
            $this->applySurveyPendingCondition($query);
        } else {
            $this->applyBeforeSequenceCondition($query, $sequence);

            if (is_numeric($minimumSequence) && $sequence !== self::FINISH_SEQUENCE) {
                $this->applyMinimumSequenceCondition($query, (int)$minimumSequence);
            }
        }

        $this->applyEventFilter($query, $eventId);

        if (is_string($entryId) && $entryId !== '') {
            $query->where(['Participants.entry_id' => $entryId]);
        }

        if ($excludeCheckedOut) {
            $query->where(['Participants.checked_out' => false]);
        }

        if (is_string($excludeCheckpointId) && $excludeCheckpointId !== '') {
            $query->notMatching('CheckIns', function (SelectQuery $query) use ($excludeCheckpointId): SelectQuery {
                return $query->where(['CheckIns.checkpoint_id' => $excludeCheckpointId]);
            });
        }

        return $query;
    }

    /**
     * Find participants who have checked in, are still walking, and are before a checkpoint sequence.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findActiveBeforeSequence(SelectQuery $query, array $options): SelectQuery
    {
        $sequence = (int)($options['sequence'] ?? 0);
        $eventId = $options['eventId'] ?? null;

        $this->applySequenceEligibility($query, $sequence);
        if ($sequence === self::SURVEY_SEQUENCE) {
            $this->applySurveyPendingCondition($query);
        } else {
            $this->applyBeforeSequenceCondition($query, $sequence);
        }

        $this->applyEventFilter($query, $eventId);

        return $query;
    }

    /**
     * Find participants currently between a previous and current checkpoint.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findBetweenSequences(SelectQuery $query, array $options): SelectQuery
    {
        return $this->findBeforeSequence($query, $options);
    }

    /**
     * Find participants who have reached or passed a checkpoint and are still walking.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findReachedSequence(SelectQuery $query, array $options): SelectQuery
    {
        $sequence = (int)($options['sequence'] ?? 0);
        $eventId = $options['eventId'] ?? null;

        $query->where(['Participants.checked_in' => true]);
        if ($sequence === self::SURVEY_SEQUENCE) {
            $this->applySurveyCompletedCondition($query);
        } else {
            $this->applyReachedSequenceCondition($query, $sequence);
        }

        $this->applyEventFilter($query, $eventId);

        return $query;
    }

    /**
     * Find participants who are still walking in an event.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findStillWalking(SelectQuery $query, array $options): SelectQuery
    {
        $sequence = $options['sequence'] ?? null;
        $eventId = $options['eventId'] ?? null;

        if (is_numeric($sequence) && (int)$sequence !== 0) {
            $query->where([
                'Participants.checked_in' => true,
                'Participants.checked_out' => false,
            ]);
        } else {
            $query->where(['Participants.checked_out' => false]);
        }

        $this->applyEventFilter($query, $eventId);

        return $query;
    }

    /**
     * Find participants who have checked out in an event.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant>
     */
    public function findCheckedOut(SelectQuery $query, array $options): SelectQuery
    {
        $eventId = $options['eventId'] ?? null;

        $query->where(['Participants.checked_out' => true]);

        $this->applyEventFilter($query, $eventId);

        return $query;
    }

    /**
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param string|null $eventId Event id.
     * @return void
     */
    protected function applyEventFilter(SelectQuery $query, ?string $eventId): void
    {
        if (!is_string($eventId) || $eventId === '') {
            return;
        }

        $query->matching('Entries', function (SelectQuery $query) use ($eventId): SelectQuery {
            return $query->where(['Entries.event_id' => $eventId]);
        });
    }

    /**
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param int $sequence Sequence.
     * @return void
     */
    protected function applyBeforeSequenceCondition(SelectQuery $query, int $sequence): void
    {
        if ($sequence === self::SURVEY_SEQUENCE) {
            $query->where(['Participants.highest_check_in_sequence !=' => self::SURVEY_SEQUENCE]);

            return;
        }

        if ($sequence === self::FINISH_SEQUENCE) {
            return;
        }

        if ($sequence === 0) {
            return;
        }

        $query->where([
            'Participants.highest_check_in_sequence <' => $sequence,
            'Participants.highest_check_in_sequence NOT IN' => [
                self::FINISH_SEQUENCE,
                self::SURVEY_SEQUENCE,
            ],
        ]);
    }

    /**
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param int $minimumSequence Sequence.
     * @return void
     */
    protected function applyMinimumSequenceCondition(SelectQuery $query, int $minimumSequence): void
    {
        if ($minimumSequence === self::FINISH_SEQUENCE) {
            $query->where([
                'Participants.highest_check_in_sequence IN' => [
                    self::FINISH_SEQUENCE,
                    self::SURVEY_SEQUENCE,
                ],
            ]);

            return;
        }

        if ($minimumSequence === self::SURVEY_SEQUENCE) {
            $query->where(['Participants.highest_check_in_sequence' => self::SURVEY_SEQUENCE]);

            return;
        }

        $query->where(['Participants.highest_check_in_sequence >=' => $minimumSequence]);
    }

    /**
     * Apply the base participant eligibility for a target checkpoint sequence.
     *
     * -2: all checked-in participants who have not already completed -2
     * -1: all checked-in participants who are not checked out
     * 0: all participants who have not checked in
     * 1+: all checked-in participants who are not checked out
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param int $sequence Sequence.
     * @return void
     */
    protected function applySequenceEligibility(SelectQuery $query, int $sequence): void
    {
        if ($sequence === self::SURVEY_SEQUENCE) {
            $query->where([
                'Participants.checked_in' => true,
            ]);

            return;
        }

        if ($sequence === self::FINISH_SEQUENCE) {
            $query->where([
                'Participants.checked_in' => true,
                'Participants.checked_out' => false,
            ]);

            return;
        }

        if ($sequence === 0) {
            $query->where([
                'Participants.checked_in' => false,
            ]);

            return;
        }

        $query->where([
            'Participants.checked_in' => true,
            'Participants.checked_out' => false,
        ]);
    }

    /**
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @param int $sequence Sequence.
     * @return void
     */
    protected function applyReachedSequenceCondition(SelectQuery $query, int $sequence): void
    {
        if ($sequence === 0) {
            $query->where(['Participants.checked_in' => true]);

            return;
        }

        if ($sequence === self::SURVEY_SEQUENCE) {
            $query->where(['Participants.highest_check_in_sequence' => self::SURVEY_SEQUENCE]);

            return;
        }

        if ($sequence === self::FINISH_SEQUENCE) {
            $query->where(['Participants.checked_out' => true]);

            return;
        }

        $query->where([
            'OR' => [
                ['Participants.highest_check_in_sequence >=' => $sequence],
                ['Participants.highest_check_in_sequence IN' => [
                    self::FINISH_SEQUENCE,
                    self::SURVEY_SEQUENCE,
                ]],
            ],
        ]);
    }

    /**
     * Participants eligible for survey are those without a direct -2 participant check-in.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @return void
     */
    protected function applySurveyPendingCondition(SelectQuery $query): void
    {
        $query->where([
            $this->aliasField('id') . ' NOT IN' => $this->buildSurveyParticipantIdsSubquery(),
        ]);
    }

    /**
     * Participants counted at survey are those with a direct -2 participant check-in.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Participant> $query Query.
     * @return void
     */
    protected function applySurveyCompletedCondition(SelectQuery $query): void
    {
        $query->where([
            $this->aliasField('id') . ' IN' => $this->buildSurveyParticipantIdsSubquery(),
        ]);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<array<string, mixed>|\Cake\Datasource\EntityInterface>
     */
    protected function buildSurveyParticipantIdsSubquery(): SelectQuery
    {
        /** @var \App\Model\Table\ParticipantsCheckInsTable $participantsCheckInsTable */
        $participantsCheckInsTable = $this->getTableLocator()->get('ParticipantsCheckIns');

        /** @var \Cake\ORM\Query\SelectQuery<array<string, mixed>|\Cake\Datasource\EntityInterface> $query */
        $query = $participantsCheckInsTable->find()
            ->select(['ParticipantsCheckIns.participant_id'])
            ->matching('CheckIns.Checkpoints', function (SelectQuery $query): SelectQuery {
                return $query->where(['Checkpoints.checkpoint_sequence' => self::SURVEY_SEQUENCE]);
            });

        return $query;
    }
}
