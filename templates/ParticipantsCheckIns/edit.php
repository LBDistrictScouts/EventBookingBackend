<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn $participantsCheckIn
 * @var \App\Model\Entity\CheckIn[]|\Cake\Collection\CollectionInterface $checkIns
 * @var \App\Model\Entity\Participant[]|\Cake\Collection\CollectionInterface $participants
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participantsCheckIn->check_in_id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantsCheckIn->check_in_id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants Check Ins'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participantsCheckIns form content">
    <?= $this->Form->create($participantsCheckIn) ?>
    <fieldset>
        <legend><?= __('Edit Participants Check In') ?></legend>
        <?php
            echo $this->Form->control('id');
            echo $this->Form->control('deleted');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
