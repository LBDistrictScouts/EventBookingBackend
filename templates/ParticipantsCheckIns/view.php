<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn $participantsCheckIn
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Participants Check In'), ['action' => 'edit', $participantsCheckIn->check_in_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Participants Check In'), ['action' => 'delete', $participantsCheckIn->check_in_id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantsCheckIn->check_in_id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Participants Check Ins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Participants Check In'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participantsCheckIns view content">
            <h3><?= h($participantsCheckIn->Array) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($participantsCheckIn->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Check In') ?></th>
                    <td><?= $participantsCheckIn->hasValue('check_in') ? $this->Html->link($participantsCheckIn->check_in->id, ['controller' => 'CheckIns', 'action' => 'view', $participantsCheckIn->check_in->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant') ?></th>
                    <td><?= $participantsCheckIn->hasValue('participant') ? $this->Html->link($participantsCheckIn->participant->first_name, ['controller' => 'Participants', 'action' => 'view', $participantsCheckIn->participant->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($participantsCheckIn->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($participantsCheckIn->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($participantsCheckIn->deleted) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>