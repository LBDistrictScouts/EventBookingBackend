<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var \App\Model\Entity\Checkpoint|null $previousCheckpoint
 * @var \Cake\Collection\CollectionInterface<int, \App\Model\Entity\Participant> $betweenParticipants
 * @var int $betweenParticipantCount
 */
?>
<div
    class="card border-0 shadow-sm ajax-table-card"
    id="checkpoint-between-table"
    data-checkpoint-fragment="table"
>
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= __('Participants In Transit') ?></span>
        <span class="badge text-bg-primary"><?= $this->Number->format($betweenParticipantCount) ?></span>
    </div>
    <div class="card-body">
        <?php if ($betweenParticipantCount > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th><?= __('Participant') ?></th>
                        <th><?= __('Entry') ?></th>
                        <th><?= __('Last Recorded Sequence') ?></th>
                        <th><?= __('Section') ?></th>
                        <th><?= __('Type') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($betweenParticipants as $participant) : ?>
                        <tr>
                            <td class="fw-semibold"><?= h($participant->full_name) ?></td>
                            <td>
                                <?= $this->Html->link(
                                    sprintf(
                                        '%s-%d',
                                        $participant->entry->event->booking_code,
                                        $participant->entry->reference_number,
                                    ),
                                    ['controller' => 'Entries', 'action' => 'view', $participant->entry_id],
                                ) ?>
                            </td>
                            <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
                            <td><?= h($participant->section?->section_name ?? '') ?></td>
                            <td><?= h($participant->participant_type?->participant_type ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="text-secondary"><?= __('No participants are currently between the previous checkpoint and this checkpoint.') ?></div>
        <?php endif; ?>
    </div>
</div>
