<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint[]|\Cake\Collection\CollectionInterface $checkpoints
 * @var bool $showAll
 * @var \App\Model\Entity\Event|null $currentEvent
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Checkpoint'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1"><?= __('Checkpoints') ?></h2>
        <div class="text-secondary small">
            <?php if ($showAll) : ?>
                <?= __('Showing all checkpoints across events.') ?>
            <?php elseif ($currentEvent !== null) : ?>
                <?= __('Showing checkpoints for the current event: {0}', h($currentEvent->event_name)) ?>
            <?php else : ?>
                <?= __('No current event is active, so all checkpoints are shown.') ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <?php if ($showAll) : ?>
            <?= $this->Html->link(__('Show Current Event Only'), ['action' => 'index'], ['class' => 'btn btn-outline-primary']) ?>
        <?php else : ?>
            <?= $this->Html->link(__('Show All Checkpoints'), ['action' => 'index', '?' => ['all' => '1']], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('checkpoint_sequence') ?></th>
        <th scope="col"><?= $this->Paginator->sort('checkpoint_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('event_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($checkpoints as $checkpoint) : ?>
        <tr>
            <td><?= $this->Number->format($checkpoint->checkpoint_sequence) ?></td>
            <td><?= h($checkpoint->checkpoint_name) ?></td>
            <td><?= $checkpoint->hasValue('event') ? $this->Html->link($checkpoint->event->event_name, ['controller' => 'Events', 'action' => 'view', $checkpoint->event->id]) : '' ?></td>
            <td><?= h($checkpoint->created) ?></td>
            <td><?= h($checkpoint->modified) ?></td>
            <td><?= h($checkpoint->deleted) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($checkpoint) ?>
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
