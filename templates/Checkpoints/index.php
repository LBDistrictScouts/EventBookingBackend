<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Checkpoint> $checkpoints
 */
?>
<div class="checkpoints index content">
    <?= $this->Html->link(__('New Checkpoint'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Checkpoints') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('checkpoint_sequence') ?></th>
                    <th><?= $this->Paginator->sort('checkpoint_name') ?></th>
                    <th><?= $this->Paginator->sort('event_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkpoints as $checkpoint): ?>
                <tr>
                    <td><?= h($checkpoint->id) ?></td>
                    <td><?= $this->Number->format($checkpoint->checkpoint_sequence) ?></td>
                    <td><?= h($checkpoint->checkpoint_name) ?></td>
                    <td><?= $checkpoint->hasValue('event') ? $this->Html->link($checkpoint->event->event_name, ['controller' => 'Events', 'action' => 'view', $checkpoint->event->id]) : '' ?></td>
                    <td><?= h($checkpoint->created) ?></td>
                    <td><?= h($checkpoint->modified) ?></td>
                    <td><?= h($checkpoint->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $checkpoint->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $checkpoint->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $checkpoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkpoint->id)]) ?>
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