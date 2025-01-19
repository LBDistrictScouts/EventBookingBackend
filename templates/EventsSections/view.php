<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EventsSection $eventsSection
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Events Section'), ['action' => 'edit', $eventsSection->section_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Events Section'), ['action' => 'delete', $eventsSection->section_id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventsSection->section_id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Events Sections'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Events Section'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="eventsSections view content">
            <h3><?= h($eventsSection->Array) ?></h3>
            <table>
                <tr>
                    <th><?= __('Section') ?></th>
                    <td><?= $eventsSection->hasValue('section') ? $this->Html->link($eventsSection->section->section_name, ['controller' => 'Sections', 'action' => 'view', $eventsSection->section->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Event') ?></th>
                    <td><?= $eventsSection->hasValue('event') ? $this->Html->link($eventsSection->event->event_name, ['controller' => 'Events', 'action' => 'view', $eventsSection->event->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($eventsSection->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($eventsSection->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($eventsSection->deleted) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>