<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var string[]|\Cake\Collection\CollectionInterface $events
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $checkpoint->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $checkpoint->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Checkpoints'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="checkpoints form content">
            <?= $this->Form->create($checkpoint) ?>
            <fieldset>
                <legend><?= __('Edit Checkpoint') ?></legend>
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
