<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Section $section
 * @var \Cake\Collection\CollectionInterface|string[] $participantTypes
 * @var \Cake\Collection\CollectionInterface|string[] $groups
 * @var \Cake\Collection\CollectionInterface|string[] $events
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Sections'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="sections form content">
            <?= $this->Form->create($section) ?>
            <fieldset>
                <legend><?= __('Add Section') ?></legend>
                <?php
                    echo $this->Form->control('section_name');
                    echo $this->Form->control('participant_type_id', ['options' => $participantTypes]);
                    echo $this->Form->control('group_id', ['options' => $groups]);
                    echo $this->Form->control('osm_section_id', ['type' => 'number', 'label' => 'OSM Section ID']);
                    echo $this->Form->control('events._ids', ['options' => $events]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
