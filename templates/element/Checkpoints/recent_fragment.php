<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 */
?>
<div
    class="card border-0 shadow-sm"
    id="checkpoint-recent-checkins"
    data-checkpoint-fragment="recent"
>
    <div class="card-header"><?= __('Recent Check Ins Here') ?></div>
    <div class="card-body">
        <?php if (!empty($checkpoint->check_ins)) : ?>
            <div class="list-group list-group-flush">
                <?php foreach ($checkpoint->check_ins as $checkIn) : ?>
                    <div class="list-group-item px-0">
                        <div class="fw-semibold">
                            <?= $this->Html->link(
                                $checkIn->entry?->entry_name ?? __('Entry'),
                                ['controller' => 'Entries', 'action' => 'view', $checkIn->entry_id],
                            ) ?>
                        </div>
                        <div class="small text-secondary"><?= h($checkIn->check_in_time) ?></div>
                        <div class="small text-secondary"><?= __('Participants: {0}', $this->Number->format($checkIn->participant_count)) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-secondary"><?= __('No check-ins recorded at this checkpoint yet.') ?></div>
        <?php endif; ?>
    </div>
</div>
