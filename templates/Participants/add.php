<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 * @var \Cake\Collection\CollectionInterface|string[] $entries
 * @var \Cake\Collection\CollectionInterface|string[] $participantTypes
 * @var \Cake\Collection\CollectionInterface|string[] $sections
 * @var \Cake\Collection\CollectionInterface|string[] $checkIns
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Participants'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participants form content">
            <?= $this->Form->create($participant) ?>
            <fieldset>
                <legend><?= __('Add Participant') ?></legend>
                <?php
                    echo $this->Form->control('first_name');
                    echo $this->Form->control('last_name');
                    echo $this->Form->control('entry_id', ['options' => $entries]);
                    echo $this->Form->control('participant_type_id', ['options' => $participantTypes]);
                    echo $this->Form->control('section_id', ['options' => $sections, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
