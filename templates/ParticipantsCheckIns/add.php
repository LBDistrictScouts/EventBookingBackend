<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn $participantsCheckIn
 * @var \Cake\Collection\CollectionInterface|string[] $checkIns
 * @var \Cake\Collection\CollectionInterface|string[] $participants
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Participants Check Ins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participantsCheckIns form content">
            <?= $this->Form->create($participantsCheckIn) ?>
            <fieldset>
                <legend><?= __('Add Participants Check In') ?></legend>
                <?php
                    echo $this->Form->control('id');
                    echo $this->Form->control('deleted');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
