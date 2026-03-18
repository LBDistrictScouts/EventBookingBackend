<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var int $beforeParticipantCount
 * @var int $betweenParticipantCount
 * @var int $checkedInHereParticipantCount
 * @var int $stillWalkingParticipantCount
 * @var int $checkedOutParticipantCount
 */
?>
<div id="checkpoint-between-count" data-checkpoint-fragment="count">
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-4 col-xxl">
            <div class="card border-0 shadow-sm h-100 ajax-table-card">
                <div class="card-body p-4 text-center">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Before Count') ?></div>
                    <div class="display-2 fw-bold mb-2"><?= $this->Number->format($beforeParticipantCount) ?></div>
                    <div class="text-secondary"><?= __('Checked in already, still walking, and before this checkpoint') ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4 col-xxl">
            <div class="card border-0 shadow-sm h-100 ajax-table-card">
                <div class="card-body p-4 text-center">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Between Count') ?></div>
                    <div class="display-2 fw-bold mb-2"><?= $this->Number->format($betweenParticipantCount) ?></div>
                    <div class="text-secondary"><?= __('Currently between the previous checkpoint and this one') ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4 col-xxl">
            <div class="card border-0 shadow-sm h-100 ajax-table-card">
                <div class="card-body p-4 text-center">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Checked In Here Count') ?></div>
                    <div class="display-2 fw-bold mb-2"><?= $this->Number->format($checkedInHereParticipantCount) ?></div>
                    <div class="text-secondary"><?= __('Participants whose highest checkpoint is this one or later') ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-6 col-xxl">
            <div class="card border-0 shadow-sm h-100 ajax-table-card">
                <div class="card-body p-4 text-center">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Total Still Walking') ?></div>
                    <div class="display-2 fw-bold mb-2"><?= $this->Number->format($stillWalkingParticipantCount) ?></div>
                    <div class="text-secondary"><?= __('All event participants who have not checked out yet') ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-6 col-xxl">
            <div class="card border-0 shadow-sm h-100 ajax-table-card">
                <div class="card-body p-4 text-center">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Total Checked Out') ?></div>
                    <div class="display-2 fw-bold mb-2"><?= $this->Number->format($checkedOutParticipantCount) ?></div>
                    <div class="text-secondary"><?= __('All event participants who have checked out') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
