<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Checkpoint'), ['action' => 'edit', $checkpoint->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Checkpoint'), ['action' => 'delete', $checkpoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkpoint->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Checkpoints'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Checkpoint'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="checkpoints view large-9 medium-8 columns content">
    <h3><?= h($checkpoint->checkpoint_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Id') ?></th>
                <td><?= h($checkpoint->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checkpoint Name') ?></th>
                <td><?= h($checkpoint->checkpoint_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Event') ?></th>
                <td><?= $checkpoint->hasValue('event') ? $this->Html->link($checkpoint->event->event_name, ['controller' => 'Events', 'action' => 'view', $checkpoint->event->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checkpoint Sequence') ?></th>
                <td><?= $this->Number->format($checkpoint->checkpoint_sequence) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($checkpoint->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($checkpoint->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($checkpoint->deleted) ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Check Ins') ?></h4>
        <?php if (!empty($checkpoint->check_ins)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Checkpoint Id') ?></th>
                    <th scope="col"><?= __('Entry Id') ?></th>
                    <th scope="col"><?= __('Check In Time') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col"><?= __('Deleted') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($checkpoint->check_ins as $checkIns): ?>
                <tr>
                    <td><?= h($checkIns->id) ?></td>
                    <td><?= h($checkIns->checkpoint_id) ?></td>
                    <td><?= h($checkIns->entry_id) ?></td>
                    <td><?= h($checkIns->check_in_time) ?></td>
                    <td><?= h($checkIns->participant_count) ?></td>
                    <td><?= h($checkIns->created) ?></td>
                    <td><?= h($checkIns->modified) ?></td>
                    <td><?= h($checkIns->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['controller' => 'CheckIns', 'action' => 'view', $checkIns->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Html->link(__('Edit'), ['controller' => 'CheckIns', 'action' => 'edit', $checkIns->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->postLink( __('Delete'), ['controller' => 'CheckIns', 'action' => 'delete', $checkIns->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIns->id), 'class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
