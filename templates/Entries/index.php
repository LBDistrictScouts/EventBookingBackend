<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Entry> $entries
 */
?>
<div class="entries index content">
    <?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Entries') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('event_id') ?></th>
                    <th><?= $this->Paginator->sort('entry_name') ?></th>
                    <th><?= $this->Paginator->sort('active') ?></th>
                    <th><?= $this->Paginator->sort('participant_count') ?></th>
                    <th><?= $this->Paginator->sort('checked_in_count') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th><?= $this->Paginator->sort('entry_email') ?></th>
                    <th><?= $this->Paginator->sort('entry_mobile') ?></th>
                    <th><?= $this->Paginator->sort('security_code') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= h($entry->id) ?></td>
                    <td><?= $entry->hasValue('event') ? $this->Html->link($entry->event->event_name, ['controller' => 'Events', 'action' => 'view', $entry->event->id]) : '' ?></td>
                    <td><?= h($entry->entry_name) ?></td>
                    <td><?= h($entry->active) ?></td>
                    <td><?= $this->Number->format($entry->participant_count) ?></td>
                    <td><?= $this->Number->format($entry->checked_in_count) ?></td>
                    <td><?= h($entry->created) ?></td>
                    <td><?= h($entry->modified) ?></td>
                    <td><?= h($entry->deleted) ?></td>
                    <td><?= h($entry->entry_email) ?></td>
                    <td><?= h($entry->entry_mobile) ?></td>
                    <td><?= h($entry->security_code) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $entry->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $entry->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id)]) ?>
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