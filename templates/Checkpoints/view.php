<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Checkpoint'), ['action' => 'edit', $checkpoint->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Checkpoint'), ['action' => 'delete', $checkpoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkpoint->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Checkpoints'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Checkpoint'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="checkpoints view content">
            <h3><?= h($checkpoint->checkpoint_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Checkpoint Name') ?></th>
                    <td><?= h($checkpoint->checkpoint_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Event') ?></th>
                    <td><?= $checkpoint->hasValue('event') ? $this->Html->link($checkpoint->event->event_name, ['controller' => 'Events', 'action' => 'view', $checkpoint->event->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($checkpoint->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Checkpoint Sequence') ?></th>
                    <td><?= $this->Number->format($checkpoint->checkpoint_sequence) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($checkpoint->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($checkpoint->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($checkpoint->deleted) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Check Ins') ?></h4>
                <?php if (!empty($checkpoint->check_ins)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Checkpoint Id') ?></th>
                            <th><?= __('Entry Id') ?></th>
                            <th><?= __('Check In Time') ?></th>
                            <th><?= __('Participant Count') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($checkpoint->check_ins as $checkIn) : ?>
                        <tr>
                            <td><?= h($checkIn->id) ?></td>
                            <td><?= h($checkIn->checkpoint_id) ?></td>
                            <td><?= h($checkIn->entry_id) ?></td>
                            <td><?= h($checkIn->check_in_time) ?></td>
                            <td><?= h($checkIn->participant_count) ?></td>
                            <td><?= h($checkIn->created) ?></td>
                            <td><?= h($checkIn->modified) ?></td>
                            <td><?= h($checkIn->deleted) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'CheckIns', 'action' => 'view', $checkIn->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'CheckIns', 'action' => 'edit', $checkIn->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'CheckIns', 'action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>