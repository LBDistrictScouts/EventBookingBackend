<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Event> $events
 */
?>
<div class="events index content">
    <?= $this->Html->link(__('New Event'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Events') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('booking_code') ?></th>
                    <th><?= $this->Paginator->sort('event_name') ?></th>
                    <th><?= $this->Paginator->sort('start_time') ?></th>
                    <th><?= $this->Paginator->sort('entry_count') ?></th>
                    <th><?= $this->Paginator->sort('participant_count') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= h($event->booking_code) ?></td>
                    <td><?= h($event->event_name) ?></td>
                    <td><?= h($event->start_time) ?></td>

                    <td><?= $this->Number->format($event->entry_count) ?></td>
                    <td><?= $this->Number->format($event->participant_count) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $event->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $event->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?>
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
