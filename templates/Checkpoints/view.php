<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Check In Walkers'), ['controller' => 'CheckIns', 'action' => 'add', 0, $checkpoint->id], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="checkpoints view large-9 medium-8 columns content">
    <h3><?= h($checkpoint->checkpoint_name) ?></h3>
    <div class="mb-3">
        <?= $this->Actions->buttons($checkpoint) ?>
    </div>
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
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Check Ins') ?></h4>
        <?php if (!empty($checkpoint->check_ins)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Entry') ?></th>
                    <th scope="col"><?= __('Check In Time') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($checkpoint->check_ins as $checkIns): ?>
                <tr>
                    <td><?= $this->Html->link(
                            title: ($checkIns->has('entry') ? $checkIns->entry->entry_name : ''),
                            url: ['controller' => 'Entries', 'action' => 'view', $checkIns->entry_id],
                        ) ?></td>
                    <td><?= h($checkIns->check_in_time) ?></td>
                    <td><?= h($checkIns->participant_count) ?></td>
                    <td><?= h($checkIns->created) ?></td>
                    <td><?= h($checkIns->modified) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($checkIns, ['outline' => true]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
