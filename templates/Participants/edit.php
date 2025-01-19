<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 * @var string[]|\Cake\Collection\CollectionInterface $entries
 * @var string[]|\Cake\Collection\CollectionInterface $participantTypes
 * @var string[]|\Cake\Collection\CollectionInterface $sections
 * @var string[]|\Cake\Collection\CollectionInterface $checkIns
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $participant->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Participants'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participants form content">
            <?= $this->Form->create($participant) ?>
            <fieldset>
                <legend><?= __('Edit Participant') ?></legend>
                <?php
                    echo $this->Form->control('first_name');
                    echo $this->Form->control('last_name');
                    echo $this->Form->control('entry_id', ['options' => $entries]);
                    echo $this->Form->control('participant_type_id', ['options' => $participantTypes]);
                    echo $this->Form->control('section_id', ['options' => $sections, 'empty' => true]);
                    echo $this->Form->control('checked_in');
                    echo $this->Form->control('checked_out');
                    echo $this->Form->control('deleted');
                    echo $this->Form->control('highest_check_in_sequence');
                    echo $this->Form->control('check_ins._ids', ['options' => $checkIns]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
