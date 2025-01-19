<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\EventsSection> $eventsSections
 */
?>
<div class="eventsSections index content">
    <?= $this->Html->link(__('New Events Section'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Events Sections') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('section_id') ?></th>
                    <th><?= $this->Paginator->sort('event_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventsSections as $eventsSection): ?>
                <tr>
                    <td><?= $eventsSection->hasValue('section') ? $this->Html->link($eventsSection->section->section_name, ['controller' => 'Sections', 'action' => 'view', $eventsSection->section->id]) : '' ?></td>
                    <td><?= $eventsSection->hasValue('event') ? $this->Html->link($eventsSection->event->event_name, ['controller' => 'Events', 'action' => 'view', $eventsSection->event->id]) : '' ?></td>
                    <td><?= h($eventsSection->created) ?></td>
                    <td><?= h($eventsSection->modified) ?></td>
                    <td><?= h($eventsSection->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $eventsSection->section_id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $eventsSection->section_id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $eventsSection->section_id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventsSection->section_id)]) ?>
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