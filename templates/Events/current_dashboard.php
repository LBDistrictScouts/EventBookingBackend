<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php $this->Html->css('current-dashboard', ['block' => true]); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Open Event View'), ['action' => 'view', $event->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Jump To Entries'), ['action' => 'view', $event->id, '#' => 'entries'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Jump To Sections'), ['action' => 'view', $event->id, '#' => 'sections'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Jump To Checkpoints'), ['action' => 'view', $event->id, '#' => 'checkpoints'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Jump To Questions'), ['action' => 'view', $event->id, '#' => 'questions'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="current-dashboard pb-4">
    <div class="dashboard-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-xl-5">
            <div class="row align-items-center g-4">
                <div class="col-xl-8">
                    <div class="text-uppercase small fw-semibold dashboard-kicker mb-2"><?= __('Active Event') ?></div>
                    <h2 class="display-5 fw-bold mb-3"><?= h($event->event_name) ?></h2>
                    <p class="lead text-secondary mb-4">
                        <?= h($event->event_description ?: __('Current live event for bookings and check-ins.')) ?>
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge dashboard-badge dashboard-badge-code"><?= __('Booking Code: {0}', h($event->booking_code)) ?></span>
                        <span class="badge text-bg-success"><?= __('Bookable') ?></span>
                        <?php if (!$event->finished) : ?>
                            <span class="badge text-bg-primary"><?= __('Live') ?></span>
                        <?php endif; ?>
                        <span class="badge dashboard-badge dashboard-badge-date"><?= h($event->start_time) ?></span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <?= $this->Html->link(
                            __('Open Event View'),
                            ['action' => 'view', $event->id],
                            ['class' => 'btn btn-dark btn-lg'],
                        ) ?>
                        <?= $this->Html->link(
                            __('Edit Event'),
                            ['action' => 'edit', $event->id],
                            ['class' => 'btn btn-outline-dark btn-lg'],
                        ) ?>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="dashboard-panel card h-100 border-0">
                        <div class="card-body">
                            <h3 class="h5 mb-3"><?= __('At a Glance') ?></h3>
                            <div class="quick-links d-grid gap-2">
                                <?= $this->Html->link(__('Entries'), ['action' => 'view', $event->id, '#' => 'entries'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                <?= $this->Html->link(__('Sections'), ['action' => 'view', $event->id, '#' => 'sections'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                <?= $this->Html->link(__('Checkpoints'), ['action' => 'view', $event->id, '#' => 'checkpoints'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                <?= $this->Html->link(__('Questions'), ['action' => 'view', $event->id, '#' => 'questions'], ['class' => 'btn btn-outline-primary text-start']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <article class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="metric-label"><?= __('Entry Count') ?></div>
                    <div class="metric-value"><?= $this->Number->format($event->entry_count) ?></div>
                    <div class="metric-note"><?= __('Total booking records in this event.') ?></div>
                </div>
            </article>
        </div>
        <div class="col-12 col-lg-4">
            <article class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="metric-label"><?= __('Participant Count') ?></div>
                    <div class="metric-value"><?= $this->Number->format($event->participant_count) ?></div>
                    <div class="metric-note"><?= __('All people attached to active entries.') ?></div>
                </div>
            </article>
        </div>
        <div class="col-12 col-lg-4">
            <article class="metric-card card border-0 shadow-sm h-100 metric-card-accent">
                <div class="card-body">
                    <div class="metric-label"><?= __('Checked In Count') ?></div>
                    <div class="metric-value"><?= $this->Number->format($event->checked_in_count) ?></div>
                    <div class="metric-note"><?= __('Participants currently checked in.') ?></div>
                </div>
            </article>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-3">
            <div class="jump-card card border-0 shadow-sm h-100" id="entries-card">
                <div class="card-body">
                    <div class="jump-eyebrow"><?= __('Event View') ?></div>
                    <h3 class="h4"><?= __('Entries') ?></h3>
                    <p class="text-secondary mb-4"><?= __('Review the booking list and drill into individual entries.') ?></p>
                    <?= $this->Html->link(__('Open Entries'), ['action' => 'view', $event->id, '#' => 'entries'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="jump-card card border-0 shadow-sm h-100" id="sections-card">
                <div class="card-body">
                    <div class="jump-eyebrow"><?= __('Event View') ?></div>
                    <h3 class="h4"><?= __('Sections') ?></h3>
                    <p class="text-secondary mb-4"><?= __('See which sections are available and how they are grouped.') ?></p>
                    <?= $this->Html->link(__('Open Sections'), ['action' => 'view', $event->id, '#' => 'sections'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="jump-card card border-0 shadow-sm h-100" id="checkpoints-card">
                <div class="card-body">
                    <div class="jump-eyebrow"><?= __('Event View') ?></div>
                    <h3 class="h4"><?= __('Checkpoints') ?></h3>
                    <p class="text-secondary mb-4"><?= __('Jump straight to check-in locations and progress flow.') ?></p>
                    <?= $this->Html->link(__('Open Checkpoints'), ['action' => 'view', $event->id, '#' => 'checkpoints'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="jump-card card border-0 shadow-sm h-100" id="questions-card">
                <div class="card-body">
                    <div class="jump-eyebrow"><?= __('Event View') ?></div>
                    <h3 class="h4"><?= __('Questions') ?></h3>
                    <p class="text-secondary mb-4"><?= __('Review the booking questions presented for this event.') ?></p>
                    <?= $this->Html->link(__('Open Questions'), ['action' => 'view', $event->id, '#' => 'questions'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</section>
