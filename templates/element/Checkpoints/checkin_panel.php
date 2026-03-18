<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var \Cake\Collection\CollectionInterface<int|string, string>|array<int|string, string> $participants
 * @var string $selectedEntryId
 * @var string $selectedEntryReference
 * @var string $selectedEntryLabel
 */
?>
<?php
$checkInTimeValue = $checkIn->check_in_time instanceof \Cake\I18n\DateTime
    ? $checkIn->check_in_time->format('Y-m-d\TH:i:s')
    : date('Y-m-d\TH:i:s');
?>
<div class="card border-0 shadow-sm ajax-table-card" id="checkpoint-checkin-panel" data-checkpoint-fragment="panel">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= __('Add Check In') ?></span>
        <span class="badge text-bg-secondary"><?= h($checkpoint->checkpoint_name) ?></span>
    </div>
    <div class="card-body">
        <form
            method="post"
            action="<?= h($this->Url->build(['controller' => 'CheckIns', 'action' => 'checkpoint', $checkpoint->id, '_ext' => 'json'])) ?>"
            data-checkpoint-checkin-form
            data-panel-url="<?= h($this->Url->build(['controller' => 'CheckIns', 'action' => 'checkpoint', $checkpoint->id])) ?>"
        >
            <?= $this->Form->hidden('_csrfToken', ['value' => $this->getRequest()->getAttribute('csrfToken')]) ?>
            <?= $this->Form->hidden('checkpoint_id', ['value' => $checkpoint->id]) ?>
            <?= $this->Form->hidden('entry_id', ['form' => false, 'value' => $selectedEntryId]) ?>
            <div class="mb-3">
                <label class="form-label" for="entry-reference"><?= __('Entry Reference') ?></label>
                <div class="input-group">
                    <input
                        type="text"
                        name="entry_reference"
                        id="entry-reference"
                        class="form-control"
                        value="<?= h($selectedEntryReference) ?>"
                        placeholder="<?= h(__('1, 21, GW26-3')) ?>"
                        autocomplete="off"
                        data-checkpoint-entry-reference="<?= h($this->Url->build(['controller' => 'CheckIns', 'action' => 'checkpoint', $checkpoint->id])) ?>"
                    >
                    <button type="button" class="btn btn-outline-secondary" data-checkpoint-entry-load>
                        <?= __('Load Entry') ?>
                    </button>
                </div>
                <div class="form-text"><?= __('Enter an event entry reference to load its available participants.') ?></div>
            </div>
            <?php if ($selectedEntryLabel !== '') : ?>
                <div class="alert alert-light border small mb-3">
                    <div class="text-uppercase text-secondary fw-semibold small mb-1"><?= __('Using Entry') ?></div>
                    <div class="fw-semibold"><?= h($selectedEntryLabel) ?></div>
                </div>
            <?php elseif ($selectedEntryReference !== '') : ?>
                <div class="alert alert-warning small mb-3">
                    <?= __('No entry was found for reference {0} in this event.', h($selectedEntryReference)) ?>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <?= $this->Form->control('check_in_time', [
                    'form' => false,
                    'value' => $checkInTimeValue,
                    'type' => 'datetime-local',
                    'label' => __('Check In Time'),
                ]) ?>
            </div>
            <?php if ($selectedEntryId !== '' && count($participants) > 0) : ?>
                <div class="mb-3">
                    <label class="form-label d-block"><?= __('Participants') ?></label>
                    <?php foreach ($participants as $participantId => $participantName) : ?>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="participants[]"
                                value="<?= h((string)$participantId) ?>"
                                id="checkpoint-participant-<?= h((string)$participantId) ?>"
                            >
                            <label class="form-check-label" for="checkpoint-participant-<?= h((string)$participantId) ?>">
                                <?= h((string)$participantName) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($selectedEntryId !== '') : ?>
                <div class="text-secondary small mb-3"><?= __('No available participants for this entry.') ?></div>
            <?php else : ?>
                <div class="text-secondary small mb-3"><?= __('Enter an entry reference to load the participant checklist.') ?></div>
            <?php endif; ?>
            <div class="d-grid">
                <button
                    type="submit"
                    class="btn btn-primary"
                    <?= $selectedEntryId === '' ? 'disabled' : '' ?>
                >
                    <?= __('Save Check In') ?>
                </button>
            </div>
        </form>
    </div>
</div>
