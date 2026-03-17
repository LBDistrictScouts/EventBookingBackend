<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $consumedEntry
 * @var \App\Model\Entity\Entry|null $survivorEntry
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $allMergeEntries
 * @var bool $isConfirmation
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php
$referenceLabel = static fn(\App\Model\Entity\Entry $entry): string => sprintf(
    '%s-%s',
    (string)$entry->event->booking_code,
    (string)$entry->reference_number,
);

$participantRows = [];
foreach ($consumedEntry->participants as $participant) {
    $participantRows[] = [
        'name' => trim((string)$participant->full_name),
        'source' => 'consumed',
        'reference' => $referenceLabel($consumedEntry),
        'checked_in' => (bool)$participant->checked_in,
        'checked_out' => (bool)$participant->checked_out,
        'highest' => (int)$participant->highest_check_in_sequence,
    ];
}
if ($survivorEntry !== null) {
    foreach ($survivorEntry->participants as $participant) {
        $participantRows[] = [
            'name' => trim((string)$participant->full_name),
            'source' => 'survivor',
            'reference' => $referenceLabel($survivorEntry),
            'checked_in' => (bool)$participant->checked_in,
            'checked_out' => (bool)$participant->checked_out,
            'highest' => (int)$participant->highest_check_in_sequence,
        ];
    }
}
?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Back To Entry'), ['action' => 'view', $consumedEntry->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<?php if ($survivorEntry !== null) : ?>
<li><?= $this->Html->link(__('Open Survivor'), ['action' => 'view', $survivorEntry->id], ['class' => 'nav-link']) ?></li>
<?php endif; ?>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="entries merge content">
    <h3><?= __('Merge Entries') ?></h3>
    <p class="text-muted">
        <?= __('This workflow permanently consumes one entry and moves its participants into the surviving entry.') ?>
    </p>

    <?php if ($isConfirmation && $survivorEntry !== null) : ?>
        <div class="alert alert-warning mb-4">
            <strong><?= __('Final confirmation required.') ?></strong>
            <?= __(' Review the roles below before performing the merge.') ?>
        </div>
    <?php endif; ?>

    <?php if (count($allMergeEntries) > 1 && !$isConfirmation) : ?>
        <?= $this->Form->create(null, [
            'url' => ['action' => 'merge', $consumedEntry->id, $survivorEntry?->id],
            'id' => 'merge-selection-form',
            'data-preview-base' => $this->Url->build(['action' => 'mergePreview']),
        ]) ?>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card border-danger h-100 shadow-sm">
                <div class="card-header bg-danger text-white"><?= __('Consumed Entry') ?></div>
                <div class="card-body" id="merge-consumed-card">
                    <div id="merge-consumed-preview">
                        <h4 class="h5" data-consumed-name><?= h($consumedEntry->entry_name) ?></h4>
                        <p class="mb-2"><strong><?= __('Reference') ?>:</strong> <span data-consumed-reference><?= h($referenceLabel($consumedEntry)) ?></span></p>
                        <p class="mb-2"><strong><?= __('Email') ?>:</strong> <span data-consumed-email><?= h((string)$consumedEntry->entry_email) ?></span></p>
                        <p class="mb-2"><strong><?= __('Mobile') ?>:</strong> <span data-consumed-mobile><?= h((string)$consumedEntry->entry_mobile) ?></span></p>
                        <p class="mb-2"><strong><?= __('Participants') ?>:</strong> <span data-consumed-participants><?= $this->Number->format(count($consumedEntry->participants)) ?></span></p>
                        <p class="mb-0 text-danger"><?= __('This entry will be deleted after its participants are moved.') ?></p>
                    </div>

                    <?php if (!$isConfirmation) : ?>
                        <?php
                        $consumedOptions = [];
                        foreach ($allMergeEntries as $candidate) {
                            $consumedOptions[$candidate->id] = sprintf(
                                '%s | %s | %d participants',
                                $referenceLabel($candidate),
                                $candidate->entry_name,
                                (int)$candidate->participant_count,
                            );
                        }
                        ?>
                        <hr>
                        <div id="merge-consumed-select-wrap">
                            <?= $this->Form->control('consumed_entry_id', [
                                'label' => __('Entry That Gets Consumed'),
                                'options' => $consumedOptions,
                                'default' => $consumedEntry->id,
                                'id' => 'consumed-entry-id',
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-header bg-success text-white"><?= __('Surviving Entry') ?></div>
                <div class="card-body" id="merge-survivor-card">
                    <?php if ($survivorEntry !== null) : ?>
                        <div id="merge-survivor-preview">
                            <h4 class="h5"><?= h($survivorEntry->entry_name) ?></h4>
                            <p class="mb-2"><strong><?= __('Reference') ?>:</strong> <span data-preview-reference><?= h($referenceLabel($survivorEntry)) ?></span></p>
                            <p class="mb-2"><strong><?= __('Email') ?>:</strong> <span data-preview-email><?= h((string)$survivorEntry->entry_email) ?></span></p>
                            <p class="mb-2"><strong><?= __('Mobile') ?>:</strong> <span data-preview-mobile><?= h((string)$survivorEntry->entry_mobile) ?></span></p>
                            <p class="mb-2"><strong><?= __('Current Participants') ?>:</strong> <span data-preview-participants><?= $this->Number->format(count($survivorEntry->participants)) ?></span></p>
                            <p class="mb-2"><strong><?= __('After Merge') ?>:</strong> <span data-preview-merged><?= $this->Number->format(count($survivorEntry->participants) + count($consumedEntry->participants)) ?></span></p>
                            <p class="mb-0 text-success"><?= __('This entry remains and receives the consumed entry participants.') ?></p>
                        </div>
                    <?php else : ?>
                        <div id="merge-survivor-preview">
                            <p class="mb-0 text-muted" data-preview-empty><?= __('Choose which entry should survive this merge.') ?></p>
                            <div class="d-none" data-preview-details>
                                <h4 class="h5" data-preview-name></h4>
                                <p class="mb-2"><strong><?= __('Reference') ?>:</strong> <span data-preview-reference></span></p>
                                <p class="mb-2"><strong><?= __('Email') ?>:</strong> <span data-preview-email></span></p>
                                <p class="mb-2"><strong><?= __('Mobile') ?>:</strong> <span data-preview-mobile></span></p>
                                <p class="mb-2"><strong><?= __('Current Participants') ?>:</strong> <span data-preview-participants></span></p>
                                <p class="mb-2"><strong><?= __('After Merge') ?>:</strong> <span data-preview-merged></span></p>
                                <p class="mb-0 text-success"><?= __('This entry remains and receives the consumed entry participants.') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (count($allMergeEntries) > 1 && !$isConfirmation) : ?>
                        <?php
                        $mergeOptions = [];
                        foreach ($allMergeEntries as $candidate) {
                            $mergeOptions[$candidate->id] = sprintf(
                                '%s | %s | %d participants',
                                $referenceLabel($candidate),
                                $candidate->entry_name,
                                (int)$candidate->participant_count,
                            );
                        }
                        ?>
                        <hr>
                        <?= $this->Form->control('persisting_entry_id', [
                            'label' => __('Entry That Survives'),
                            'options' => $mergeOptions,
                            'empty' => __('Choose the surviving entry'),
                            'default' => $survivorEntry?->id,
                            'id' => 'persisting-entry-id',
                        ]) ?>
                        <div class="alert alert-light border d-none" id="merge-selection-status"></div>
                    <?php elseif ($isConfirmation && $survivorEntry !== null) : ?>
                        <hr>
                        <p class="mb-3">
                            <?= __(
                                'You are about to consume {0} and keep {1}. This cannot be undone from the interface.',
                                $referenceLabel($consumedEntry),
                                $referenceLabel($survivorEntry),
                            ) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($allMergeEntries) <= 1) : ?>
        <div class="alert alert-secondary mb-0">
            <?= __('No other entries in this event are available as merge targets.') ?>
        </div>
    <?php else : ?>
        <div class="card shadow-sm">
            <div class="card-header"><?= __('Participants In Scope') ?></div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-3 small">
                    <span class="badge text-bg-danger"><?= __('Consumed Entry Participant') ?></span>
                    <span class="badge text-bg-success"><?= __('Surviving Entry Participant') ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th><?= __('Participant') ?></th>
                                <th><?= __('Source Entry') ?></th>
                                <th><?= __('In') ?></th>
                                <th><?= __('Out') ?></th>
                                <th><?= __('#') ?></th>
                            </tr>
                        </thead>
                        <tbody id="merge-participant-table-body">
                            <?php if ($participantRows !== []) : ?>
                                <?php foreach ($participantRows as $row) : ?>
                                    <tr class="<?= $row['source'] === 'consumed' ? 'table-danger' : 'table-success' ?>">
                                        <td><?= h($row['name']) ?></td>
                                        <td><?= h($row['reference']) ?></td>
                                        <td><?= $this->BooleanIcon->render($row['checked_in']) ?></td>
                                        <td><?= $this->BooleanIcon->render($row['checked_out']) ?></td>
                                        <td><?= $this->Number->format($row['highest']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr id="merge-participant-empty-row">
                                    <td colspan="5" class="text-muted"><?= __('Select a surviving entry to preview the combined participant list.') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($allMergeEntries) > 1 && !$isConfirmation) : ?>
        <div class="d-flex gap-2 justify-content-end mt-4">
            <?= $this->Form->button(__('Review Merge'), [
                'class' => 'btn btn-warning',
                'id' => 'merge-review-button',
                'disabled' => $survivorEntry === null,
            ]) ?>
            <?= $this->Html->link(__('Cancel'), ['action' => 'view', $consumedEntry->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
        <?= $this->Form->end() ?>
    <?php endif; ?>

    <?php if ($isConfirmation && $survivorEntry !== null) : ?>
        <div class="d-flex gap-2 justify-content-end mt-4">
            <?= $this->Form->create(null, ['url' => ['action' => 'merge', $consumedEntry->id, $survivorEntry->id]]) ?>
            <?= $this->Form->hidden('consumed_entry_id', ['value' => $consumedEntry->id]) ?>
            <?= $this->Form->hidden('persisting_entry_id', ['value' => $survivorEntry->id]) ?>
            <?= $this->Form->hidden('confirmed', ['value' => 1]) ?>
            <?= $this->Form->button(__('Confirm Merge'), ['class' => 'btn btn-danger']) ?>
            <?= $this->Form->end() ?>
            <?= $this->Html->link(__('Change Selection'), ['action' => 'merge', $consumedEntry->id, $survivorEntry->id], ['class' => 'btn btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Cancel'), ['action' => 'view', $consumedEntry->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    <?php endif; ?>
</div>

<?php if (count($allMergeEntries) > 1 && !$isConfirmation) : ?>
<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('merge-selection-form');
    const consumedSelect = document.getElementById('consumed-entry-id');
    const select = document.getElementById('persisting-entry-id');
    const reviewButton = document.getElementById('merge-review-button');
    const status = document.getElementById('merge-selection-status');
    const preview = document.getElementById('merge-survivor-preview');
    const consumedPreview = document.getElementById('merge-consumed-preview');
    const participantTableBody = document.getElementById('merge-participant-table-body');

    if (!form || !consumedSelect || !select || !reviewButton || !preview || !consumedPreview || !participantTableBody) {
        return;
    }

    const consumedName = consumedPreview.querySelector('[data-consumed-name]');
    const consumedReference = consumedPreview.querySelector('[data-consumed-reference]');
    const consumedEmail = consumedPreview.querySelector('[data-consumed-email]');
    const consumedMobile = consumedPreview.querySelector('[data-consumed-mobile]');
    const consumedParticipants = consumedPreview.querySelector('[data-consumed-participants]');
    const emptyState = preview.querySelector('[data-preview-empty]');
    const details = preview.querySelector('[data-preview-details]');
    const name = preview.querySelector('[data-preview-name]');
    const reference = preview.querySelector('[data-preview-reference]');
    const email = preview.querySelector('[data-preview-email]');
    const mobile = preview.querySelector('[data-preview-mobile]');
    const participants = preview.querySelector('[data-preview-participants]');
    const merged = preview.querySelector('[data-preview-merged]');
    const base = form.dataset.previewBase;

    const escapeHtml = function (value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const renderCheck = function (value) {
        return value ? '<i class="bi bi-check-circle"></i>' : '';
    };

    const renderParticipants = function (data) {
        const rows = [];
        for (const participant of data.consumed.participants) {
            rows.push(`
                <tr class="table-danger">
                    <td>${escapeHtml(participant.name)}</td>
                    <td>${escapeHtml(data.consumed.reference)}</td>
                    <td>${renderCheck(participant.checked_in)}</td>
                    <td>${renderCheck(participant.checked_out)}</td>
                    <td>${escapeHtml(participant.highest_check_in_sequence)}</td>
                </tr>
            `);
        }
        for (const participant of data.survivor.participants) {
            rows.push(`
                <tr class="table-success">
                    <td>${escapeHtml(participant.name)}</td>
                    <td>${escapeHtml(data.survivor.reference)}</td>
                    <td>${renderCheck(participant.checked_in)}</td>
                    <td>${renderCheck(participant.checked_out)}</td>
                    <td>${escapeHtml(participant.highest_check_in_sequence)}</td>
                </tr>
            `);
        }

        participantTableBody.innerHTML = rows.join('');
    };

    const resetPreview = function () {
        reviewButton.disabled = true;
        status.classList.add('d-none');
        status.textContent = '';
        if (emptyState) {
            emptyState.classList.remove('d-none');
        }
        if (details) {
            details.classList.add('d-none');
        }
        participantTableBody.innerHTML = `
            <tr id="merge-participant-empty-row">
                <td colspan="5" class="text-muted">Select a surviving entry to preview the combined participant list.</td>
            </tr>
        `;
    };

    const showError = function (message) {
        status.className = 'alert alert-danger border';
        status.textContent = message;
        reviewButton.disabled = true;
    };

    const applyConsumed = function (data) {
        if (consumedName) {
            consumedName.textContent = data.consumed.entry_name;
        }
        if (consumedReference) {
            consumedReference.textContent = data.consumed.reference;
        }
        if (consumedEmail) {
            consumedEmail.textContent = data.consumed.entry_email || '';
        }
        if (consumedMobile) {
            consumedMobile.textContent = data.consumed.entry_mobile || '';
        }
        if (consumedParticipants) {
            consumedParticipants.textContent = data.consumed.participant_count;
        }
    };

    const applyPreview = function (data) {
        applyConsumed(data);
        if (emptyState) {
            emptyState.classList.add('d-none');
        }
        if (details) {
            details.classList.remove('d-none');
        }
        if (name) {
            name.textContent = data.survivor.entry_name;
        }
        if (reference) {
            reference.textContent = data.survivor.reference;
        }
        if (email) {
            email.textContent = data.survivor.entry_email || '';
        }
        if (mobile) {
            mobile.textContent = data.survivor.entry_mobile || '';
        }
        if (participants) {
            participants.textContent = data.survivor.participant_count;
        }
        if (merged) {
            merged.textContent = data.merged_participant_count;
        }
        renderParticipants(data);
        status.className = 'alert alert-success border';
        status.textContent = 'Merge preview loaded.';
        reviewButton.disabled = false;
    };

    const updatePreview = async function () {
        const consumedId = consumedSelect.value;
        const survivorId = select.value;
        if (!consumedId || !survivorId) {
            resetPreview();
            return;
        }
        if (consumedId === survivorId) {
            showError('Consumed and surviving entries must be different.');
            participantTableBody.innerHTML = `
                <tr id="merge-participant-empty-row">
                    <td colspan="5" class="text-muted">Choose two different entries to preview the combined participant list.</td>
                </tr>
            `;
            return;
        }

        status.className = 'alert alert-light border';
        status.textContent = 'Loading merge preview...';
        status.classList.remove('d-none');
        reviewButton.disabled = true;

        try {
            const response = await fetch(base + '/' + consumedId + '/' + survivorId + '.json', {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            });
            if (!response.ok) {
                throw new Error('Preview request failed');
            }
            const payload = await response.json();
            applyPreview(payload.preview);
        } catch (error) {
            showError('Unable to load merge preview for that entry.');
        }
    };

    consumedSelect.addEventListener('change', updatePreview);
    select.addEventListener('change', updatePreview);

    if (consumedSelect.value && select.value) {
        updatePreview();
    } else {
        resetPreview();
    }
});
</script>
<?php $this->end(); ?>
<?php endif; ?>
