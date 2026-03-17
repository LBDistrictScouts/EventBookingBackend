<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Checkpoint> $checkpoints
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Entry> $entries
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Participant> $participants
 * @var string|null $entryId
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('List Check Ins'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="checkIns form content">
    <?= $this->Form->create($checkIn) ?>
    <fieldset>
        <legend><?= __('Add Check In') ?></legend>
        <?php
            echo $this->Form->control('entry_id', [
                'options' => $entries,
                'empty' => __('Choose an entry'),
                'value' => $entryId,
                'data-redirect-on-change' => $this->Url->build(['action' => 'add']),
            ]);
            echo $this->Form->control('checkpoint_id', [
                'options' => $checkpoints,
                'empty' => $entryId ? __('Choose a checkpoint') : __('Choose an entry first'),
                'disabled' => $entryId === null || $entryId === '',
            ]);
            echo $this->Form->control('check_in_time');
        ?>
        <?php if ($entryId !== null && $entryId !== '') : ?>
            <?= $this->Form->multiCheckbox('participants._ids', $participants) ?>
        <?php else : ?>
            <div class="text-secondary small mt-3">
                <?= __('Choose an entry to load the participant checklist for that booking.') ?>
            </div>
        <?php endif; ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const entrySelect = document.querySelector('[data-redirect-on-change]');
    if (!(entrySelect instanceof HTMLSelectElement)) {
        return;
    }

    entrySelect.addEventListener('change', function () {
        const baseUrl = entrySelect.dataset.redirectOnChange;
        if (!baseUrl) {
            return;
        }

        if (entrySelect.value) {
            window.location.assign(baseUrl.replace(/\/$/, '') + '/' + encodeURIComponent(entrySelect.value));
            return;
        }

        window.location.assign(baseUrl);
    });
});
</script>
