<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EventsSection $eventsSection
 * @var \Cake\Collection\CollectionInterface|string[] $sections
 * @var \Cake\Collection\CollectionInterface|string[] $events
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Events Sections'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="eventsSections form content">
            <?= $this->Form->create($eventsSection) ?>
            <fieldset>
                <legend><?= __('Add Events Section') ?></legend>
                <?php
                    echo $this->Form->control('deleted');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
