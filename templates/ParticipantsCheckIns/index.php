<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ParticipantsCheckIn> $participantsCheckIns
 */
?>
<div class="participantsCheckIns index content">
    <?= $this->Html->link(__('New Participants Check In'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Participants Check Ins') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('check_in_id') ?></th>
                    <th><?= $this->Paginator->sort('participant_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participantsCheckIns as $participantsCheckIn): ?>
                <tr>
                    <td><?= h($participantsCheckIn->id) ?></td>
                    <td><?= $participantsCheckIn->hasValue('check_in') ? $this->Html->link($participantsCheckIn->check_in->id, ['controller' => 'CheckIns', 'action' => 'view', $participantsCheckIn->check_in->id]) : '' ?></td>
                    <td><?= $participantsCheckIn->hasValue('participant') ? $this->Html->link($participantsCheckIn->participant->first_name, ['controller' => 'Participants', 'action' => 'view', $participantsCheckIn->participant->id]) : '' ?></td>
                    <td><?= h($participantsCheckIn->created) ?></td>
                    <td><?= h($participantsCheckIn->modified) ?></td>
                    <td><?= h($participantsCheckIn->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $participantsCheckIn->check_in_id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $participantsCheckIn->check_in_id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participantsCheckIn->check_in_id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantsCheckIn->check_in_id)]) ?>
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