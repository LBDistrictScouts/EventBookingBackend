<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Checkpoint> $checkpoints
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Entry> $entries
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Participant> $participants
 * @var bool $entryFixed
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
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
        <legend><?= __('Add Check In') ?></legend>
        <?php
            echo $this->Form->control('checkpoint_id', ['options' => $checkpoints]);
            echo $this->Form->control('entry_id', ['options' => $entries, 'disabled' => $entryFixed]);
            echo $this->Form->control('check_in_time');
            echo $entryFixed ?
                $this->Form->multiCheckbox('participants._ids', $participants) :
                $this->Form->control('participants._ids', ['options' => $participants]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
