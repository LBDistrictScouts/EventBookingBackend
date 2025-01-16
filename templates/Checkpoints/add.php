<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var \Cake\Collection\CollectionInterface|string[] $events
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Checkpoints'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="checkpoints form content">
            <?= $this->Form->create($checkpoint) ?>
            <fieldset>
                <legend><?= __('Add Checkpoint') ?></legend>
                <?php
                    echo $this->Form->control('checkpoint_sequence');
                    echo $this->Form->control('checkpoint_name');
                    echo $this->Form->control('event_id', ['options' => $events]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
