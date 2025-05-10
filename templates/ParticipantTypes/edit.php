<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType $participantType
 * @var \App\Model\Entity\Participant[]|\Cake\Collection\CollectionInterface $participants
 * @var \App\Model\Entity\Section[]|\Cake\Collection\CollectionInterface $sections
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participantType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participantTypes form content">
    <?= $this->Form->create($participantType) ?>
    <fieldset>
        <legend><?= __('Edit Participant Type') ?></legend>
        <?php
            echo $this->Form->control('participant_type');
            echo $this->Form->control('adult');
            echo $this->Form->control('uniformed');
            echo $this->Form->control('out_of_district');
            echo $this->Form->control('deleted');
            echo $this->Form->control('category');
            echo $this->Form->control('sort_order');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
