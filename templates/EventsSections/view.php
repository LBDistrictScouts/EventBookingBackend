<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EventsSection $eventsSection
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Events Section'), ['action' => 'edit', $eventsSection->section_id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Events Section'), ['action' => 'delete', $eventsSection->section_id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventsSection->section_id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events Sections'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Events Section'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="eventsSections view large-9 medium-8 columns content">
    <h3><?= h($eventsSection->Array) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Section') ?></th>
                <td><?= $eventsSection->hasValue('section') ? $this->Html->link($eventsSection->section->section_name, ['controller' => 'Sections', 'action' => 'view', $eventsSection->section->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Event') ?></th>
                <td><?= $eventsSection->hasValue('event') ? $this->Html->link($eventsSection->event->event_name, ['controller' => 'Events', 'action' => 'view', $eventsSection->event->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($eventsSection->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($eventsSection->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($eventsSection->deleted) ?></td>
            </tr>
        </table>
    </div>
</div>
