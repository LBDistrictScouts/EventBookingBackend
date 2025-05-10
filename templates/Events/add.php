<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 * @var \App\Model\Entity\Checkpoint[]|\Cake\Collection\CollectionInterface $checkpoints
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $entries
 * @var \App\Model\Entity\Question[]|\Cake\Collection\CollectionInterface $questions
 * @var \App\Model\Entity\Section[]|\Cake\Collection\CollectionInterface $sections
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('List Events'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="events form content">
    <?= $this->Form->create($event) ?>
    <fieldset>
        <legend><?= __('Add Event') ?></legend>
        <?php
            echo $this->Form->control('event_name');
            echo $this->Form->control('event_description');
            echo $this->Form->control('booking_code');
            echo $this->Form->control('start_time');
            echo $this->Form->control('bookable');
            echo $this->Form->control('finished');
            echo $this->Form->control('entry_count');
            echo $this->Form->control('participant_count');
            echo $this->Form->control('checked_in_count');
            echo $this->Form->control('deleted');
            echo $this->Form->control('sections._ids', ['options' => $sections]);
                ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
