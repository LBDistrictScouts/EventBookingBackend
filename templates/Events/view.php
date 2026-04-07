<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php $this->Html->css('event-view', ['block' => true]); ?>
<?php $this->Html->css('checkpoint-progress-chart', ['block' => true]); ?>

<?php
$entryCount = $event->entry_count ?? 0;
$participantCount = $event->participant_count ?? 0;
$checkedInCount = $event->checked_in_count ?? 0;
?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Add Entry'), ['controller' => 'Entries', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Add Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Add Question'), ['controller' => 'Questions', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Add Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['action' => 'index'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="event-view pb-4">
    <div class="event-view-shell mx-auto">
        <div class="event-hero card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-xl-5">
                <div class="row g-4 align-items-start">
                    <div class="col-12 col-xl-8">
                        <div class="text-uppercase small fw-semibold event-kicker mb-2"><?= __('Event') ?></div>
                        <h2 class="display-6 fw-bold mb-2"><?= h($event->event_name) ?></h2>
                        <div class="event-meta-line mb-3">
                            <span><?= h($event->booking_code) ?></span>
                            <span class="event-meta-separator">/</span>
                            <span><?= h($event->start_time) ?></span>
                            <span class="event-meta-separator">/</span>
                            <span><?= $event->bookable ? __('Bookable') : __('Not Bookable') ?></span>
                            <span class="event-meta-separator">/</span>
                            <span><?= $event->finished ? __('Finished') : __('Live') ?></span>
                        </div>
                        <p class="lead text-secondary mb-0">
                            <?= h($event->event_description ?: __('This event is currently configured for bookings and check-ins.')) ?>
                        </p>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="card border-0 event-panel h-100">
                            <div class="card-body">
                                <h3 class="h5 mb-3"><?= __('Jump To') ?></h3>
                                <div class="d-grid gap-2">
                                    <?= $this->Html->link(__('Entries'), ['action' => 'view', $event->id, '#' => 'entries'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Checkpoint Progress'), ['action' => 'view', $event->id, '#' => 'checkpoint-progress'], ['class' => 'btn btn-outline-primary text-start']) ?>
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
            <div class="col-12 col-md-4">
                <article class="event-stat card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="event-stat-label"><?= __('Entry Count') ?></div>
                        <div class="event-stat-value"><?= $this->Number->format($entryCount) ?></div>
                        <div class="event-stat-note"><?= __('Booking records attached to this event') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="event-stat card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="event-stat-label"><?= __('Participant Count') ?></div>
                        <div class="event-stat-value"><?= $this->Number->format($participantCount) ?></div>
                        <div class="event-stat-note"><?= __('People registered across all entries') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="event-stat card border-0 shadow-sm h-100 event-stat-accent">
                    <div class="card-body">
                        <div class="event-stat-label"><?= __('Checked In Count') ?></div>
                        <div class="event-stat-value"><?= $this->Number->format($checkedInCount) ?></div>
                        <div class="event-stat-note"><?= __('Participants currently marked in') ?></div>
                    </div>
                </article>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8">
                <div class="mb-4" id="checkpoint-progress">
                    <?= $this->element('Dashboard/checkpoint_progress_chart', [
                        'progress' => $checkpointProgress,
                        'title' => __('Checkpoint Progress'),
                        'description' => __('See how many participants have reached each checkpoint as their highest recorded point in this event.'),
                        'emptyMessage' => __('No checkpoint progress has been recorded for this event yet.'),
                    ]) ?>
                </div>

                <div class="mb-4" id="entries">
                    <?= $this->element('Events/entries_table', compact('event', 'entries', 'entriesPagination', 'entriesSearch')) ?>
                </div>

                <div class="mb-4" id="sections">
                    <?= $this->element('Events/sections_table', compact('event', 'sections', 'sectionsPagination')) ?>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><?= __('Event Details') ?></div>
                    <div class="card-body">
                        <div class="event-detail-grid">
                            <div>
                                <div class="event-detail-label"><?= __('Booking Code') ?></div>
                                <div class="event-detail-value"><code><?= h($event->booking_code) ?></code></div>
                            </div>
                            <div>
                                <div class="event-detail-label"><?= __('Start Time') ?></div>
                                <div class="event-detail-value"><?= h($event->start_time) ?></div>
                            </div>
                            <div>
                                <div class="event-detail-label"><?= __('Bookable') ?></div>
                                <div class="event-detail-value"><?= $event->bookable ? __('Yes') : __('No') ?></div>
                            </div>
                            <div>
                                <div class="event-detail-label"><?= __('Finished') ?></div>
                                <div class="event-detail-value"><?= $event->finished ? __('Yes') : __('No') ?></div>
                            </div>
                            <div>
                                <div class="event-detail-label"><?= __('Created') ?></div>
                                <div class="event-detail-value"><?= h($event->created) ?></div>
                            </div>
                            <div>
                                <div class="event-detail-label"><?= __('Modified') ?></div>
                                <div class="event-detail-value"><?= h($event->modified) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header"><?= __('Quick Actions') ?></div>
                    <div class="card-body d-grid gap-2">
                        <?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?= $this->Html->link(__('Add Entry'), ['controller' => 'Entries', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'btn btn-outline-primary text-start']) ?>
                        <?= $this->Html->link(__('Add Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'btn btn-outline-primary text-start']) ?>
                        <?= $this->Html->link(__('Add Question'), ['controller' => 'Questions', 'action' => 'add', '?' => ['event_id' => $event->id]], ['class' => 'btn btn-outline-primary text-start']) ?>
                        <?= $this->Html->link(__('View All Events'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], [
                            'confirm' => __('Are you sure you want to delete # {0}?', $event->id),
                            'class' => 'btn btn-outline-danger text-start',
                        ]) ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4" id="checkpoints">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Checkpoints') ?></span>
                        <span class="badge text-bg-info border"><?= $this->Number->format(count($event->checkpoints)) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($event->checkpoints)) : ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($event->checkpoints as $checkpoint) : ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <div class="event-detail-label"><?= __('Sequence {0}', $this->Number->format($checkpoint->checkpoint_sequence)) ?></div>
                                                <div class="fw-semibold"><?= h($checkpoint->checkpoint_name) ?></div>
                                            </div>
                                            <div class="actions text-nowrap"><?= $this->Actions->buttons($checkpoint, ['outline' => true]) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-secondary"><?= __('No checkpoints have been configured yet.') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm" id="questions">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Questions') ?></span>
                        <span class="badge text-bg-info border"><?= $this->Number->format(count($event->questions)) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($event->questions)) : ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($event->questions as $question) : ?>
                                    <div class="list-group-item px-0">
                                        <div class="fw-semibold mb-1"><?= h($question->question_text) ?></div>
                                        <div class="small text-secondary"><?= h($question->answer_text ?: __('No helper text')) ?></div>
                                        <div class="mt-3 actions"><?= $this->Actions->buttons($question, ['outline' => true]) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-secondary"><?= __('No booking questions have been added yet.') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $this->start('script'); ?>
<script>
const ajaxTableSearchTimers = new Map();

async function loadAjaxTable(url, targetId) {
    const currentContainer = document.getElementById(targetId);
    if (!currentContainer) {
        window.location.assign(url);
        return;
    }

    currentContainer.classList.add('is-loading');

    try {
        const response = await fetch(url, {
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        const html = await response.text();
        const documentFragment = new DOMParser().parseFromString(html, 'text/html');
        const replacement = documentFragment.getElementById(targetId);
        if (!replacement) {
            throw new Error('Replacement table not found');
        }

        currentContainer.replaceWith(replacement);
        window.history.replaceState({}, '', url);
    } catch (error) {
        window.location.assign(url);
    }
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-ajax-table-link]');
    if (!link || link.classList.contains('disabled')) {
        return;
    }

    event.preventDefault();
    void loadAjaxTable(link.href, link.dataset.ajaxTarget);
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-ajax-table-form]');
    if (!form) {
        return;
    }

    event.preventDefault();
    const url = new URL(form.action, window.location.origin);
    const formData = new FormData(form);
    const searchParams = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        const stringValue = String(value).trim();
        if (stringValue !== '') {
            searchParams.set(key, stringValue);
        }
    }

    url.search = searchParams.toString();
    void loadAjaxTable(url.toString(), form.dataset.ajaxTarget);
});

document.addEventListener('input', (event) => {
    const input = event.target.closest('input[data-ajax-search-input]');
    if (!input) {
        return;
    }

    const form = input.form;
    if (!form || !form.dataset.ajaxTableForm) {
        return;
    }

    const timerKey = form.dataset.ajaxTarget;
    const existingTimer = ajaxTableSearchTimers.get(timerKey);
    if (existingTimer) {
        window.clearTimeout(existingTimer);
    }

    const nextTimer = window.setTimeout(() => {
        form.requestSubmit();
        ajaxTableSearchTimers.delete(timerKey);
    }, 250);

    ajaxTableSearchTimers.set(timerKey, nextTimer);
});
</script>
<?php $this->end(); ?>
