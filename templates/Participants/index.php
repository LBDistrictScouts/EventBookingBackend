<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant[]|\Cake\Collection\CollectionInterface $participants
 * @var bool $showAll
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
    <div>
        <?php if ($showAll) : ?>
            <?= $this->Html->link(__('Show Current Event Only'), ['action' => 'index'], ['class' => 'btn btn-outline-primary']) ?>
        <?php else : ?>
            <?= $this->Html->link(__('Show All Participants'), ['action' => 'index', '?' => ['all' => '1']], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('first_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('last_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('entry_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_type_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('section_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('checked_in') ?></th>
        <th scope="col"><?= $this->Paginator->sort('checked_out') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col"><?= $this->Paginator->sort('highest_check_in_sequence') ?></th>
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
            <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($participant) ?>
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
