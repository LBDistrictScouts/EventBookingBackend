<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant[]|\Cake\Collection\CollectionInterface $participants
 * @var string $participantsSearch
 * @var bool $showAll
 * @var bool $showDeleted
 * @var \App\Model\Entity\Event|null $currentEvent
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Participant'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<?php
$currentQuery = $this->request->getQueryParams();
$baseQuery = $currentQuery;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1"><?= __('Participants') ?></h2>
        <div class="text-secondary small">
            <?php if ($showAll) : ?>
                <?= __('Showing participants across all events.') ?>
            <?php elseif ($currentEvent !== null) : ?>
                <?= __('Showing participants for the current event: {0}', h($currentEvent->event_name)) ?>
            <?php else : ?>
                <?= __('No current event is active, so participants from all events are shown.') ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if ($showAll) : ?>
            <?= $this->Html->link(
                __('Show Current Event Only'),
                ['action' => 'index', '?' => array_diff_key($currentQuery, ['all' => true])],
                ['class' => 'btn btn-outline-primary'],
            ) ?>
        <?php else : ?>
            <?= $this->Html->link(
                __('Show All Participants'),
                ['action' => 'index', '?' => $currentQuery + ['all' => '1']],
                ['class' => 'btn btn-outline-secondary'],
            ) ?>
        <?php endif; ?>
        <?php if ($showDeleted) : ?>
            <?= $this->Html->link(
                __('Hide Deleted Participants'),
                ['action' => 'index', '?' => array_diff_key($currentQuery, ['deleted' => true])],
                ['class' => 'btn btn-outline-primary'],
            ) ?>
        <?php else : ?>
            <?= $this->Html->link(
                __('Show Deleted Participants'),
                ['action' => 'index', '?' => $currentQuery + ['deleted' => '1']],
                ['class' => 'btn btn-outline-secondary'],
            ) ?>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
        <?php foreach ($baseQuery as $key => $value) : ?>
            <?= $this->Form->hidden($key, ['value' => $value]) ?>
        <?php endforeach; ?>
        <div class="mb-0">
            <?= $this->Form->label('participants_search', __('Search Participants'), ['class' => 'form-label']) ?>
            <div class="input-group">
                <?= $this->Form->control('participants_search', [
                    'label' => false,
                    'value' => $participantsSearch,
                    'placeholder' => __('Search by participant or entry name'),
                    'class' => 'form-control',
                    'templates' => [
                        'inputContainer' => '{{content}}',
                    ],
                ]) ?>
                <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Html->link(
                    __('Clear'),
                    ['action' => 'index', '?' => array_diff_key($currentQuery, ['participants_search' => true])],
                    ['class' => 'btn btn-outline-secondary'],
                ) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('Participants.first_name', __('First Name')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.last_name', __('Last Name')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.entry_id', __('Entry')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.participant_type_id', __('Participant Type')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.section_id', __('Section')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.checked_in', __('Checked In')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.checked_out', __('Checked Out')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.created', __('Created')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.modified', __('Modified')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.deleted', __('Deleted')) ?></th>
        <th scope="col"><?= $this->Paginator->sort('Participants.highest_check_in_sequence', __('Highest Check In Sequence')) ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($participants as $participant) : ?>
        <tr>
            <td><?= h($participant->first_name) ?></td>
            <td><?= h($participant->last_name) ?></td>
            <td><?= $participant->hasValue('entry') ? $this->Html->link($participant->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $participant->entry->id]) : '' ?></td>
            <td><?= $participant->hasValue('participant_type') ? $this->Html->link($participant->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $participant->participant_type->id]) : '' ?></td>
            <td><?= $participant->hasValue('section') ? $this->Html->link($participant->section->section_name, ['controller' => 'Sections', 'action' => 'view', $participant->section->id]) : '' ?></td>
            <td><?= h($participant->checked_in) ?></td>
            <td><?= h($participant->checked_out) ?></td>
            <td><?= h($participant->created) ?></td>
            <td><?= h($participant->modified) ?></td>
            <td><?= h($participant->deleted) ?></td>
            <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($participant, ['deleted' => $participant->deleted !== null]) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->first('«', ['label' => __('First')]) ?>
        <?= $this->Paginator->prev('‹', ['label' => __('Previous')]) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('›', ['label' => __('Next')]) ?>
        <?= $this->Paginator->last('»', ['label' => __('Last')]) ?>
    </ul>
    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
</div>
