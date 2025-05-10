<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn $participantsCheckIn
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Participants Check In'), ['action' => 'edit', $participantsCheckIn->check_in_id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Participants Check In'), ['action' => 'delete', $participantsCheckIn->check_in_id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantsCheckIn->check_in_id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants Check Ins'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Participants Check In'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participantsCheckIns view large-9 medium-8 columns content">
    <h3><?= h($participantsCheckIn->Array) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Id') ?></th>
                <td><?= h($participantsCheckIn->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Check In') ?></th>
                <td><?= $participantsCheckIn->hasValue('check_in') ? $this->Html->link($participantsCheckIn->check_in->id, ['controller' => 'CheckIns', 'action' => 'view', $participantsCheckIn->check_in->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant') ?></th>
                <td><?= $participantsCheckIn->hasValue('participant') ? $this->Html->link($participantsCheckIn->participant->first_name, ['controller' => 'Participants', 'action' => 'view', $participantsCheckIn->participant->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($participantsCheckIn->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($participantsCheckIn->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($participantsCheckIn->deleted) ?></td>
            </tr>
        </table>
    </div>
</div>
