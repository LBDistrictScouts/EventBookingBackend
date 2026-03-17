<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 * @var \App\Model\Entity\Event[]|\Cake\Collection\CollectionInterface $events
 * @var \Cake\Collection\CollectionInterface|array<string, string> $participantTypes
 * @var \Cake\Collection\CollectionInterface|array<string, mixed> $sections
 * @var list<array{first_name: string, last_name: string, participant_type_id: string, section_id: string}> $participantRows
 * @var array<int, array<string, mixed>> $participantErrors
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="entries form content">
    <?= $this->Form->create($entry) ?>
    <fieldset>
        <legend><?= __('Add Entry') ?></legend>
        <?php
            echo $this->Form->control('event_id', ['options' => $events]);
            echo $this->Form->control('entry_name');
            echo $this->Form->control('entry_email');
            echo $this->Form->control('entry_mobile');
        ?>
    </fieldset>
    <fieldset class="mt-4">
        <legend><?= __('Participants') ?></legend>
        <p class="text-muted mb-3"><?= __('Add participants now, or leave this blank and add them later.') ?></p>
        <div data-participant-rows>
            <?php foreach ($participantRows as $index => $participantRow) : ?>
                <div class="card mb-3" data-participant-row>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <?= $this->Form->control("participants.{$index}.first_name", [
                                    'label' => __('First Name'),
                                    'value' => $participantRow['first_name'],
                                ]) ?>
                                <?php foreach ((array)($participantErrors[$index]['first_name'] ?? []) as $error) : ?>
                                    <div class="text-danger small"><?= h((string)$error) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control("participants.{$index}.last_name", [
                                    'label' => __('Last Name'),
                                    'value' => $participantRow['last_name'],
                                ]) ?>
                                <?php foreach ((array)($participantErrors[$index]['last_name'] ?? []) as $error) : ?>
                                    <div class="text-danger small"><?= h((string)$error) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control("participants.{$index}.participant_type_id", [
                                    'label' => __('Participant Type'),
                                    'options' => $participantTypes,
                                    'empty' => true,
                                    'value' => $participantRow['participant_type_id'],
                                ]) ?>
                                <?php foreach ((array)($participantErrors[$index]['participant_type_id'] ?? []) as $error) : ?>
                                    <div class="text-danger small"><?= h((string)$error) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control("participants.{$index}.section_id", [
                                    'label' => __('Section'),
                                    'options' => $sections,
                                    'empty' => true,
                                    'value' => $participantRow['section_id'],
                                ]) ?>
                                <?php foreach ((array)($participantErrors[$index]['section_id'] ?? []) as $error) : ?>
                                    <div class="text-danger small"><?= h((string)$error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-danger btn-sm" data-remove-participant-row>
                                <?= __('Remove Participant') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-outline-primary" data-add-participant-row>
            <?= __('Add Participant') ?>
        </button>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>

<template id="participant-row-template">
    <div class="card mb-3" data-participant-row>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" data-participant-label="first_name"><?= __('First Name') ?></label>
                    <input type="text" class="form-control" data-participant-input="first_name">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-participant-label="last_name"><?= __('Last Name') ?></label>
                    <input type="text" class="form-control" data-participant-input="last_name">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-participant-label="participant_type_id"><?= __('Participant Type') ?></label>
                    <select class="form-select" data-participant-input="participant_type_id">
                        <option value=""><?= __('Choose one') ?></option>
                        <?php foreach ($participantTypes as $participantTypeId => $participantTypeName) : ?>
                            <option value="<?= h((string)$participantTypeId) ?>"><?= h((string)$participantTypeName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-participant-label="section_id"><?= __('Section') ?></label>
                    <select class="form-select" data-participant-input="section_id">
                        <option value=""><?= __('Choose one') ?></option>
                        <?php foreach ($sections as $sectionGroup => $sectionOptions) : ?>
                            <?php if (is_array($sectionOptions) || $sectionOptions instanceof Traversable) : ?>
                                <optgroup label="<?= h((string)$sectionGroup) ?>">
                                    <?php foreach ($sectionOptions as $sectionId => $sectionName) : ?>
                                        <option value="<?= h((string)$sectionId) ?>"><?= h((string)$sectionName) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php else : ?>
                                <option value="<?= h((string)$sectionGroup) ?>"><?= h((string)$sectionOptions) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-outline-danger btn-sm" data-remove-participant-row>
                    <?= __('Remove Participant') ?>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsContainer = document.querySelector('[data-participant-rows]');
    const addButton = document.querySelector('[data-add-participant-row]');
    const template = document.getElementById('participant-row-template');

    if (!rowsContainer || !addButton || !template) {
        return;
    }

    const setRowIndex = function (row, index) {
        row.querySelectorAll('[data-participant-input]').forEach(function (input) {
            const field = input.getAttribute('data-participant-input');
            input.name = 'participants[' + index + '][' + field + ']';
            input.id = 'participants-' + index + '-' + field.replace(/_/g, '-');
        });

        row.querySelectorAll('[data-participant-label]').forEach(function (label) {
            const field = label.getAttribute('data-participant-label');
            label.setAttribute('for', 'participants-' + index + '-' + field.replace(/_/g, '-'));
        });
    };

    const refreshIndexes = function () {
        rowsContainer.querySelectorAll('[data-participant-row]').forEach(function (row, index) {
            setRowIndex(row, index);
        });
    };

    addButton.addEventListener('click', function () {
        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector('[data-participant-row]');
        rowsContainer.appendChild(fragment);
        refreshIndexes();
        const lastRow = rowsContainer.querySelectorAll('[data-participant-row]');
        if (lastRow.length > 0) {
            const firstInput = lastRow[lastRow.length - 1].querySelector('input, select');
            if (firstInput) {
                firstInput.focus();
            }
        }
    });

    rowsContainer.addEventListener('click', function (event) {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.matches('[data-remove-participant-row]')) {
            return;
        }

        const rows = rowsContainer.querySelectorAll('[data-participant-row]');
        if (rows.length === 1) {
            rows[0].querySelectorAll('input, select').forEach(function (input) {
                input.value = '';
            });

            return;
        }

        const row = target.closest('[data-participant-row]');
        if (row) {
            row.remove();
            refreshIndexes();
        }
    });

    refreshIndexes();
});
</script>
