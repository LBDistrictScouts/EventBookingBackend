<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CheckIn> $checkIns
 */
?>
<div class="checkIns index content">
    <?= $this->Html->link(__('New Check In'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Check Ins') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('checkpoint_id') ?></th>
                    <th><?= $this->Paginator->sort('entry_id') ?></th>
                    <th><?= $this->Paginator->sort('check_in_time') ?></th>
                    <th><?= $this->Paginator->sort('participant_count') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkIns as $checkIn): ?>
                <tr>
                    <td><?= h($checkIn->id) ?></td>
                    <td><?= $checkIn->hasValue('checkpoint') ? $this->Html->link($checkIn->checkpoint->checkpoint_name, ['controller' => 'Checkpoints', 'action' => 'view', $checkIn->checkpoint->id]) : '' ?></td>
                    <td><?= $checkIn->hasValue('entry') ? $this->Html->link($checkIn->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $checkIn->entry->id]) : '' ?></td>
                    <td><?= h($checkIn->check_in_time) ?></td>
                    <td><?= $this->Number->format($checkIn->participant_count) ?></td>
                    <td><?= h($checkIn->created) ?></td>
                    <td><?= h($checkIn->modified) ?></td>
                    <td><?= h($checkIn->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $checkIn->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $checkIn->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>