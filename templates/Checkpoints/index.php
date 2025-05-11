<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint[]|\Cake\Collection\CollectionInterface $checkpoints
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
