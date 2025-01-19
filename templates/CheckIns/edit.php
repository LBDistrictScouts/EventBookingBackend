<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 * @var string[]|\Cake\Collection\CollectionInterface $checkpoints
 * @var string[]|\Cake\Collection\CollectionInterface $entries
 * @var string[]|\Cake\Collection\CollectionInterface $participants
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $checkIn->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Check Ins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="checkIns form content">
            <?= $this->Form->create($checkIn) ?>
            <fieldset>
                <legend><?= __('Edit Check In') ?></legend>
                <?php
                    echo $this->Form->control('checkpoint_id', ['options' => $checkpoints]);
                    echo $this->Form->control('entry_id', ['options' => $entries]);
                    echo $this->Form->control('check_in_time');
                    echo $this->Form->control('participant_count');
                    echo $this->Form->control('participants._ids', ['options' => $participants]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
