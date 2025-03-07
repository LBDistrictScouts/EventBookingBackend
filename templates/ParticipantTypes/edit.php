<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType $participantType
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $participantType->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Participant Types'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participantTypes form content">
            <?= $this->Form->create($participantType) ?>
            <fieldset>
                <legend><?= __('Edit Participant Type') ?></legend>
                <?php
                    echo $this->Form->control('participant_type');
                    echo $this->Form->control('adult');
                    echo $this->Form->control('uniformed');
                    echo $this->Form->control('out_of_district');
                    echo $this->Form->control('category');
                    echo $this->Form->control('sort_order');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
