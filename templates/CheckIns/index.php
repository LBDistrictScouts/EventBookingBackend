<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\CheckIn> $checkIns
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Check In'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('checkpoint_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('entry_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('check_in_time') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_count') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($checkIns as $checkIn) : ?>
        <tr>
            <td><?= $checkIn->hasValue('checkpoint') ? $this->Html->link(
                title: $checkIn->checkpoint->checkpoint_name,
                url: ['controller' => 'Checkpoints', 'action' => 'view', $checkIn->checkpoint->id],
                ) : ''
                ?></td>
            <td><?= $checkIn->hasValue('entry') ? $this->Html->link($checkIn->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $checkIn->entry->id]) : '' ?></td>
            <td><?= h($checkIn->check_in_time) ?></td>
            <td><?= $this->Number->format($checkIn->participant_count) ?></td>
            <td><?= h($checkIn->created) ?></td>
            <td><?= h($checkIn->modified) ?></td>
            <td><?= h($checkIn->deleted) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($checkIn) ?>
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
