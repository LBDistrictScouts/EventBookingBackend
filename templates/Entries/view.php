<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php $this->Html->css('entry-view', ['block' => true]); ?>

<?php
$reference = $entry->event->booking_code . '-' . $this->Number->format($entry->reference_number);
$participantCount = count($entry->participants);
$checkInCount = count($entry->check_ins);
?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Entry'), ['action' => 'edit', $entry->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Merge Entry'), ['action' => 'merge', $entry->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Entry'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="entry-view pb-4">
    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="entry-hero card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-xl-5">
                    <div class="row g-4 align-items-start">
                        <div class="col-12">
                            <div class="text-uppercase small fw-semibold entry-kicker mb-2"><?= __('Entry') ?></div>
                            <h2 class="display-6 fw-bold mb-2"><?= h($entry->entry_name) ?></h2>
                            <div class="entry-meta-line mb-3">
                                <span><?= h($reference) ?></span>
                                <span class="entry-meta-separator">/</span>
                                <span><?= h($entry->event->event_name) ?></span>
                                <span class="entry-meta-separator">/</span>
                                <span><?= $entry->active ? __('Active') : __('Inactive') ?></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 entry-panel h-100">
                                <div class="card-body">
                                    <h3 class="h5 mb-3"><?= __('Contact') ?></h3>
                                    <div class="entry-contact-line">
                                        <div class="entry-contact-label"><?= __('Email') ?></div>
                                        <div><?= $this->Text->autoLinkEmails($entry->entry_email) ?></div>
                                    </div>
                                    <div class="entry-contact-line">
                                        <div class="entry-contact-label"><?= __('Mobile') ?></div>
                                        <div><?= h($entry->entry_mobile ?: __('Not provided')) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <article class="entry-stat card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="entry-stat-label"><?= __('Participants') ?></div>
                            <div class="entry-stat-value"><?= $this->Number->format($entry->participant_count) ?></div>
                            <div class="entry-stat-note"><?= __('Registered on this entry') ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="entry-stat card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="entry-stat-label"><?= __('Checked In') ?></div>
                            <div class="entry-stat-value"><?= $this->Number->format($entry->checked_in_count) ?></div>
                            <div class="entry-stat-note"><?= __('Currently marked as in') ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="entry-stat card border-0 shadow-sm h-100 entry-stat-accent">
                        <div class="card-body">
                            <div class="entry-stat-label"><?= __('Check In Records') ?></div>
                            <div class="entry-stat-value"><?= $this->Number->format($checkInCount) ?></div>
                            <div class="entry-stat-note"><?= __('Logged checkpoints for this entry') ?></div>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-xxl-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header"><?= __('Entry Details') ?></div>
                        <div class="card-body">
                            <div class="entry-detail-grid">
                                <div>
                                    <div class="entry-detail-label"><?= __('Reference') ?></div>
                                    <div class="entry-detail-value"><?= h($reference) ?></div>
                                </div>
                                <div>
                                    <div class="entry-detail-label"><?= __('Created') ?></div>
                                    <div class="entry-detail-value"><?= h($entry->created) ?></div>
                                </div>
                                <div>
                                    <div class="entry-detail-label"><?= __('Modified') ?></div>
                                    <div class="entry-detail-value"><?= h($entry->modified) ?></div>
                                </div>
                                <div>
                                    <div class="entry-detail-label"><?= __('Active') ?></div>
                                    <div class="entry-detail-value"><?= $entry->active ? __('Yes') : __('No') ?></div>
                                </div>
                                <div>
                                    <div class="entry-detail-label"><?= __('Security Code') ?></div>
                                    <div class="entry-detail-value"><code><?= h($entry->security_code) ?></code></div>
                                </div>
                                <div>
                                    <div class="entry-detail-label"><?= __('Event') ?></div>
                                    <div class="entry-detail-value"><?= $this->Html->link($entry->event->event_name, ['controller' => 'Events', 'action' => 'view', $entry->event->id]) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xxl-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header"><?= __('Quick Actions') ?></div>
                        <div class="card-body d-grid gap-2">
                            <?= $this->Html->link(__('Edit Entry'), ['action' => 'edit', $entry->id], ['class' => 'btn btn-outline-secondary text-start']) ?>
                            <?= $this->Html->link(__('Add Participant'), ['controller' => 'Participants', 'action' => 'add', $entry->id], ['class' => 'btn btn-outline-primary text-start']) ?>
                            <?= $this->Html->link(__('Add Check In'), ['controller' => 'CheckIns', 'action' => 'add', $entry->id], ['class' => 'btn btn-outline-primary text-start']) ?>
                            <?= $this->Html->link(__('Open Merge Tool'), ['action' => 'merge', $entry->id], ['class' => 'btn btn-outline-danger text-start']) ?>
                            <?= $this->Form->postLink(__('Delete Entry'), ['action' => 'delete', $entry->id], [
                                'confirm' => __('Are you sure you want to delete # {0}?', $entry->id),
                                'class' => 'btn btn-outline-danger text-start',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?= __('Participants') ?></span>
                    <span class="badge text-bg-info border"><?= $this->Number->format($participantCount) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($entry->participants)) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= __('Participant') ?></th>
                                        <th><?= __('Type') ?></th>
                                        <th><?= __('Section') ?></th>
                                        <th><?= __('In') ?></th>
                                        <th><?= __('Out') ?></th>
                                        <th><?= __('Highest') ?></th>
                                        <th class="actions"><?= __('Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($entry->participants as $participant) : ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= h(trim($participant->full_name)) ?></div>
                                                <div class="small text-secondary"><?= h($participant->created) ?></div>
                                            </td>
                                            <td><?= $participant->has('participant_type') ? h($participant->participant_type->participant_type) : '' ?></td>
                                            <td>
                                                <?= $participant->has('section') && $participant->section !== null && $participant->section->has('section_name')
                                                    ? $this->Html->link($participant->section->section_name, ['controller' => 'Sections', 'action' => 'view', $participant->section_id])
                                                    : '' ?>
                                            </td>
                                            <td><?= $this->BooleanIcon->render($participant->checked_in) ?></td>
                                            <td><?= $this->BooleanIcon->render($participant->checked_out) ?></td>
                                            <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
                                            <td class="actions"><?= $this->Actions->buttons($participant, ['outline' => true]) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="p-4 text-secondary"><?= __('No participants have been added to this entry yet.') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card shadow-sm entry-timeline-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?= __('Check In History') ?></span>
                    <span class="badge text-bg-info"><?= $this->Number->format($checkInCount) ?></span>
                </div>
                <div class="card-body">
                    <?php if (!empty($entry->check_ins)) : ?>
                        <div class="entry-checkin-tree">
                            <?php foreach ($entry->check_ins as $checkIn) : ?>
                                <div class="entry-checkin-node">
                                    <div class="entry-checkin-rail">
                                        <div class="entry-checkin-dot"></div>
                                    </div>
                                    <div class="entry-checkin-card-wrap">
                                        <div class="entry-checkin-content card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                                    <div>
                                                        <div class="entry-checkin-sequence">
                                                            <?= __('Checkpoint {0}', $this->Number->format($checkIn->checkpoint->checkpoint_sequence)) ?>
                                                        </div>
                                                        <h4 class="h6 mb-1"><?= h($checkIn->checkpoint->checkpoint_name) ?></h4>
                                                    </div>
                                                    <div class="text-end ms-auto">
                                                        <div class="entry-checkin-time"><?= h($checkIn->check_in_time) ?></div>
                                                        <div class="entry-checkin-count"><?= __('{0} participants', $this->Number->format($checkIn->participant_count)) ?></div>
                                                        <div class="small text-secondary"><?= __('Recorded {0}', h($checkIn->created)) ?></div>
                                                    </div>
                                                </div>
                                                <div class="entry-checkin-participants mt-3">
                                                    <div class="entry-checkin-participants-label"><?= __('Participants At This Check-In') ?></div>
                                                    <?php if (!empty($checkIn->participants)) : ?>
                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            <?php foreach ($checkIn->participants as $participant) : ?>
                                                                <span class="badge text-bg-secondary entry-checkin-participant">
                                                                    <?= h(trim($participant->full_name)) ?>
                                                                    <?php if ($participant->has('participant_type')) : ?>
                                                                        <span>· <?= h($participant->participant_type->participant_type) ?></span>
                                                                    <?php endif; ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else : ?>
                                                        <div class="small text-secondary mt-2"><?= __('No participant breakdown recorded for this check-in.') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex justify-content-end mt-3">
                                                    <?= $this->Actions->buttons($checkIn, ['outline' => true]) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="p-4 text-secondary"><?= __('No check-ins have been recorded for this entry yet.') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
