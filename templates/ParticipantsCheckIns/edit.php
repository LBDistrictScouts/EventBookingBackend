<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn $participantsCheckIn
 * @var string[]|\Cake\Collection\CollectionInterface $checkIns
 * @var string[]|\Cake\Collection\CollectionInterface $participants
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $participantsCheckIn->check_in_id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $participantsCheckIn->check_in_id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Participants Check Ins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participantsCheckIns form content">
            <?= $this->Form->create($participantsCheckIn) ?>
            <fieldset>
                <legend><?= __('Edit Participants Check In') ?></legend>
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
