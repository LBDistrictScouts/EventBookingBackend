<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $entries
 * @var \App\Model\Entity\Event[]|\Cake\Collection\CollectionInterface $events
 * @var \App\Model\Entity\ParticipantType[]|\Cake\Collection\CollectionInterface $participantTypes
 * @var \App\Model\Entity\Section[]|\Cake\Collection\CollectionInterface $sections
 * @var \App\Model\Entity\CheckIn[]|\Cake\Collection\CollectionInterface $checkIns
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participants form content">
    <?= $this->Form->create($participant) ?>
    <fieldset>
        <legend><?= __('Edit Participant') ?></legend>
        <?php
            echo $this->Form->control('first_name');
            echo $this->Form->control('last_name');
            echo $this->Form->control('entry_id', ['options' => $entries]);
            echo $this->Form->control('participant_type_id', ['options' => $participantTypes]);
            echo $this->Form->control('section_id', ['options' => $sections, 'empty' => true]);
            echo $this->Form->control('checked_in');
            echo $this->Form->control('checked_out');
            echo $this->Form->control('deleted');
            echo $this->Form->control('highest_check_in_sequence');
            echo $this->Form->control('check_ins._ids', ['options' => $checkIns]);
                ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
