<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $entries
 * @var \App\Model\Entity\Event|null $currentEvent
 * @var bool $showAll
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <?php if ($showAll) : ?>
            <div class="text-secondary"><?= __('Showing entries from all events.') ?></div>
        <?php elseif ($currentEvent !== null) : ?>
            <div class="text-secondary">
                <?= __('Showing entries for the current event: {0}', h($currentEvent->event_name)) ?>
            </div>
        <?php else : ?>
            <div class="text-secondary"><?= __('No current active event was found. Showing all entries.') ?></div>
        <?php endif; ?>
    </div>
    <div>
        <?php if ($showAll) : ?>
            <?= $this->Html->link(__('Show Current Event Only'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?php else : ?>
            <?= $this->Html->link(__('Show All Entries'), ['action' => 'index', '?' => ['all' => 1]], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('reference_number') ?></th>
        <th scope="col"><?= $this->Paginator->sort('entry_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('event_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_count') ?></th>
        <th scope="col"><?= $this->Paginator->sort('checked_in_count') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($entries as $entry) : ?>
        <tr>
            <td><?= h($entry->event->booking_code) ?>-<?= $this->Number->format($entry->reference_number) ?></td>
            <td><?= h($entry->entry_name) ?></td>
            <td><?= $entry->hasValue('event') ? $this->Html->link($entry->event->event_name, ['controller' => 'Events', 'action' => 'view', $entry->event->id]) : '' ?></td>
            <td><?= $this->Number->format($entry->participant_count) ?></td>
            <td><?= $this->Number->format($entry->checked_in_count) ?></td>
            <td><?= h($entry->created) ?></td>
            <td><?= h($entry->modified) ?></td>

            <td class="actions">
                <?= $this->Actions->buttons($entry, ['outline' => false]) ?>
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
