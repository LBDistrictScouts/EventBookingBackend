<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Section $section
 * @var list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}> $teams
 * @var bool $showAll
 * @var \App\Model\Entity\Event|null $currentEvent
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php $this->Html->css('section-view', ['block' => true]); ?>
<?php $this->Html->css('checkpoint-progress-chart', ['block' => true]); ?>

<?php
$teamCount = count($teams);
$sectionParticipantCount = array_sum(array_map(
    static fn(array $team): int => $team['section_participant_count'],
    $teams,
));
$linkedEventCount = count((array)$section->events);
?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Section'), ['action' => 'edit', $section->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Section'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Section'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="section-view pb-4">
    <div class="section-view-shell mx-auto">
        <div class="section-hero card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-xl-5">
                <div class="row g-4 align-items-start">
                    <div class="col-12 col-xl-8">
                        <div class="text-uppercase small fw-semibold section-kicker mb-2"><?= __('Section') ?></div>
                        <h2 class="display-6 fw-bold mb-2"><?= h($section->section_name) ?></h2>
                        <div class="section-meta-line mb-3">
                            <span><?= $section->hasValue('group') ? h($section->group->group_name) : __('No Group') ?></span>
                            <span class="section-meta-separator">/</span>
                            <span><?= $section->hasValue('participant_type') ? h($section->participant_type->participant_type) : __('No Type') ?></span>
                            <?php if ($section->osm_section_id !== null) : ?>
                                <span class="section-meta-separator">/</span>
                                <span><?= __('OSM {0}', $this->Number->format($section->osm_section_id)) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="lead text-secondary mb-0">
                            <?= __('Monitor the teams signed up for this section and quickly inspect the participants belonging to it.') ?>
                        </p>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="card border-0 section-panel h-100">
                            <div class="card-body">
                                <h3 class="h5 mb-3"><?= __('Jump To') ?></h3>
                                <div class="d-grid gap-2">
                                    <?= $this->Html->link(__('Teams Signed Up'), ['action' => 'view', $section->id, '#' => 'teams'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Checkpoint Progress'), ['action' => 'view', $section->id, '#' => 'checkpoint-progress'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Section Details'), ['action' => 'view', $section->id, '#' => 'details'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Linked Events'), ['action' => 'view', $section->id, '#' => 'events'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <article class="section-stat card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="section-stat-label"><?= __('Teams Signed Up') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($teamCount) ?></div>
                        <div class="section-stat-note"><?= __('Distinct bookings with at least one participant in this section') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="section-stat card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="section-stat-label"><?= __('Section Participants') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($sectionParticipantCount) ?></div>
                        <div class="section-stat-note"><?= __('People currently attached to this section') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="section-stat card border-0 shadow-sm h-100 section-stat-accent">
                    <div class="card-body">
                        <div class="section-stat-label"><?= __('Linked Events') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($linkedEventCount) ?></div>
                        <div class="section-stat-note"><?= __('Events this section is available on') ?></div>
                    </div>
                </article>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8" id="teams">
                <div class="mb-4" id="checkpoint-progress">
                    <?= $this->element('Dashboard/checkpoint_progress_chart', [
                        'progress' => $checkpointProgress,
                        'title' => __('Checkpoint Progress'),
                        'description' => __('Track the highest checkpoint reached by participants in this section within the current view.'),
                        'emptyMessage' => __('No checkpoint progress has been recorded for participants in this section yet.'),
                    ]) ?>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Teams Signed Up') ?></span>
                        <span class="badge text-bg-info border"><?= $this->Number->format($teamCount) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div class="small text-secondary">
                                <?php if ($showAll) : ?>
                                    <?= __('Showing teams from all events linked to this section.') ?>
                                <?php elseif ($currentEvent !== null) : ?>
                                    <?= __('Showing teams for the current event: {0}', h($currentEvent->event_name)) ?>
                                <?php else : ?>
                                    <?= __('No current event is active, so teams from all events are shown.') ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($showAll) : ?>
                                    <?= $this->Html->link(__('Show Current Event Only'), ['action' => 'view', $section->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                                <?php else : ?>
                                    <?= $this->Html->link(__('Show All Events'), ['action' => 'view', $section->id, '?' => ['all' => '1']], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($teams !== []) : ?>
                            <div class="section-team-stack">
                                <?php foreach ($teams as $team) : ?>
                                    <?php
                                    $entry = $team['entry'];
                                    $event = $entry->event;
                                    $reference = $event !== null
                                        ? $event->booking_code . '-' . $this->Number->format($entry->reference_number)
                                        : null;
                                    ?>
                                    <article class="section-team-card">
                                        <div class="section-team-card__header">
                                            <div>
                                                <div class="section-team-card__eyebrow">
                                                    <?= $event !== null ? h($event->event_name) : __('Event') ?>
                                                </div>
                                                <h3 class="h4 mb-1"><?= h($entry->entry_name) ?></h3>
                                                <div class="section-team-card__meta">
                                                    <?php if ($reference !== null) : ?>
                                                        <span><code><?= h($reference) ?></code></span>
                                                        <span class="section-meta-separator">/</span>
                                                    <?php endif; ?>
                                                    <span><?= __('{0} total', $this->Number->format($team['participant_count'])) ?></span>
                                                    <span class="section-meta-separator">/</span>
                                                    <span><?= __('{0} in section', $this->Number->format($team['section_participant_count'])) ?></span>
                                                </div>
                                            </div>
                                            <div class="actions text-nowrap">
                                                <?= $this->Actions->buttons($entry, ['outline' => true]) ?>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12 col-lg-4">
                                                <div class="section-team-subcard">
                                                    <div class="section-detail-label"><?= __('Contact') ?></div>
                                                    <div class="fw-semibold"><?= $this->Text->autoLinkEmails((string)$entry->entry_email) ?></div>
                                                    <div class="text-secondary small"><?= h($entry->entry_mobile ?: __('No mobile')) ?></div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-8">
                                                <div class="section-team-subcard">
                                                    <div class="section-detail-label mb-2"><?= __('Participants In This Section') ?></div>
                                                    <div class="section-pill-list">
                                                        <?php foreach ($team['section_participants'] as $participant) : ?>
                                                            <span class="section-pill">
                                                                <?= h(trim($participant->full_name)) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="section-team-roster mt-3">
                                            <div class="section-detail-label mb-2"><?= __('Full Team Roster') ?></div>
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th><?= __('Name') ?></th>
                                                            <th><?= __('Type') ?></th>
                                                            <th><?= __('Section') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ((array)$entry->participants as $participant) : ?>
                                                            <?php $isSectionParticipant = $participant->section_id === $section->id; ?>
                                                            <tr class="<?= $isSectionParticipant ? 'table-primary-subtle' : '' ?>">
                                                                <td class="fw-semibold"><?= h(trim($participant->full_name)) ?></td>
                                                                <td><?= $participant->has('participant_type') ? h($participant->participant_type->participant_type) : '' ?></td>
                                                                <td><?= $participant->has('section') && $participant->section !== null ? h($participant->section->section_name) : __('No section') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-secondary"><?= __('No teams have signed up with participants in this section yet.') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card shadow-sm mb-4" id="details">
                    <div class="card-header"><?= __('Section Details') ?></div>
                    <div class="card-body">
                        <div class="section-detail-grid">
                            <div>
                                <div class="section-detail-label"><?= __('Group') ?></div>
                                <div class="section-detail-value"><?= $section->hasValue('group') ? h($section->group->group_name) : __('None') ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Participant Type') ?></div>
                                <div class="section-detail-value"><?= $section->hasValue('participant_type') ? h($section->participant_type->participant_type) : __('None') ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Notification Email') ?></div>
                                <div class="section-detail-value"><?= h($section->notification_email ?: __('Not set')) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('OSM Section ID') ?></div>
                                <div class="section-detail-value"><?= $section->osm_section_id === null ? __('Not set') : $this->Number->format($section->osm_section_id) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Created') ?></div>
                                <div class="section-detail-value"><?= h($section->created) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Modified') ?></div>
                                <div class="section-detail-value"><?= h($section->modified) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header"><?= __('Quick Actions') ?></div>
                    <div class="card-body d-grid gap-2">
                        <?= $this->Html->link(__('Edit Section'), ['action' => 'edit', $section->id], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?=
                        $section->hasValue('group')
                            ? $this->Html->link(__('View Group'), ['controller' => 'Groups', 'action' => 'view', $section->group->id], ['class' => 'btn btn-outline-secondary text-start'])
                            : ''
                        ?>
                        <?= $this->Html->link(__('Add Participant'), ['controller' => 'Participants', 'action' => 'add', '?' => ['section_id' => $section->id]], ['class' => 'btn btn-outline-primary text-start']) ?>
                        <?= $this->Html->link(__('View All Sections'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?= $this->Form->postLink(__('Delete Section'), ['action' => 'delete', $section->id], [
                            'confirm' => __('Are you sure you want to delete # {0}?', $section->id),
                            'class' => 'btn btn-outline-danger text-start',
                        ]) ?>
                    </div>
                </div>

                <div class="card shadow-sm" id="events">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Linked Events') ?></span>
                        <span class="badge text-bg-info border"><?= $this->Number->format($linkedEventCount) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($section->events)) : ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($section->events as $event) : ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <div class="fw-semibold"><?= h($event->event_name) ?></div>
                                                <div class="small text-secondary">
                                                    <?= h($event->booking_code) ?>
                                                    <span class="section-meta-separator">/</span>
                                                    <?= h($event->start_time) ?>
                                                </div>
                                            </div>
                                            <div class="actions text-nowrap"><?= $this->Actions->buttons($event, ['outline' => true]) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-secondary"><?= __('This section is not linked to any events yet.') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
