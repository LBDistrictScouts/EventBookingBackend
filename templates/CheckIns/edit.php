<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 * @var \App\Model\Entity\Checkpoint[]|\Cake\Collection\CollectionInterface $checkpoints
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $entries
 * @var \App\Model\Entity\Participant[]|\Cake\Collection\CollectionInterface $participants
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="checkIns form content">
    <?= $this->Form->create($checkIn) ?>
    <fieldset>
        <legend><?= __('Edit Check In') ?></legend>
        <?php
            echo $this->Form->control('checkpoint_id', ['options' => $checkpoints]);
            echo $this->Form->control('entry_id', ['options' => $entries]);
            echo $this->Form->control('check_in_time');
            echo $this->Form->control('participant_count');
            echo $this->Form->control('participants._ids', ['options' => $participants]);
                ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
