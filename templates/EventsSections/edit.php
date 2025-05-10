<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EventsSection $eventsSection
 * @var \App\Model\Entity\Section[]|\Cake\Collection\CollectionInterface $sections
 * @var \App\Model\Entity\Event[]|\Cake\Collection\CollectionInterface $events
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $eventsSection->section_id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventsSection->section_id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events Sections'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="eventsSections form content">
    <?= $this->Form->create($eventsSection) ?>
    <fieldset>
        <legend><?= __('Edit Events Section') ?></legend>
        <?php
            echo $this->Form->control('deleted');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
