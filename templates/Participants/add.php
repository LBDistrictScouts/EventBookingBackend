<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Entry> $entries
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Event> $events
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\ParticipantType> $participantTypes
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Section> $sections
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\CheckIn> $checkIns
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
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
        <legend><?= __('Add Participant') ?></legend>
        <?php
            echo $this->Form->control('first_name');
            echo $this->Form->control('last_name');
            echo $this->Form->control('entry_id', ['options' => $entries]);
            echo $this->Form->control('participant_type_id', ['options' => $participantTypes]);
            echo $this->Form->control('section_id', ['options' => $sections, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
