<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Group $group
 * @var list<array{section: \App\Model\Entity\Section, teams: list<array{entry: \App\Model\Entity\Entry, section_participants: list<\App\Model\Entity\Participant>, participant_count: int, section_participant_count: int}>, team_count: int, section_participant_count: int}> $sectionSummaries
 * @var bool $showAll
 * @var \App\Model\Entity\Event|null $currentEvent
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php $this->Html->css('section-view', ['block' => true]); ?>
<?php $this->Html->css('checkpoint-progress-chart', ['block' => true]); ?>

<?php
$sectionCount = count((array)$group->sections);
$teamCount = array_sum(array_map(
    static fn(array $summary): int => $summary['team_count'],
    $sectionSummaries,
));
$participantCount = array_sum(array_map(
    static fn(array $summary): int => $summary['section_participant_count'],
    $sectionSummaries,
));
?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Group'), ['action' => 'edit', $group->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Group'), ['action' => 'delete', $group->id], ['confirm' => __('Are you sure you want to delete {0}?', $group->group_name), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Groups'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Group'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="section-view pb-4">
    <div class="section-view-shell mx-auto">
        <div class="section-hero card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-xl-5">
                <div class="row g-4 align-items-start">
                    <div class="col-12 col-xl-8">
                        <div class="text-uppercase small fw-semibold section-kicker mb-2"><?= __('Group') ?></div>
                        <h2 class="display-6 fw-bold mb-2"><?= h($group->group_name) ?></h2>
                        <div class="section-meta-line mb-3">
                            <span><?= $group->visible ? __('Visible') : __('Hidden') ?></span>
                            <span class="section-meta-separator">/</span>
                            <span><?= __('Sort {0}', $this->Number->format($group->sort_order)) ?></span>
                        </div>
                        <p class="lead text-secondary mb-0">
                            <?= __('Review the sections in this group and the teams signed up through each section.') ?>
                        </p>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="card border-0 section-panel h-100">
                            <div class="card-body">
                                <h3 class="h5 mb-3"><?= __('Jump To') ?></h3>
                                <div class="d-grid gap-2">
                                    <?= $this->Html->link(__('Sections'), ['action' => 'view', $group->id, '#' => 'sections'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Checkpoint Progress'), ['action' => 'view', $group->id, '#' => 'checkpoint-progress'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?= $this->Html->link(__('Group Details'), ['action' => 'view', $group->id, '#' => 'details'], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?php if (isset($billing)) : ?>
                                        <?= $this->Html->link(__('Billing Snapshot'), ['action' => 'view', $group->id, '#' => 'billing', '?' => ['event_id' => $this->request->getQuery('event_id')]], ['class' => 'btn btn-outline-primary text-start']) ?>
                                    <?php endif; ?>
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
                        <div class="section-stat-label"><?= __('Sections') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($sectionCount) ?></div>
                        <div class="section-stat-note"><?= __('Sections currently configured in this group') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="section-stat card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="section-stat-label"><?= __('Teams Signed Up') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($teamCount) ?></div>
                        <div class="section-stat-note"><?= __('Distinct bookings represented across this group') ?></div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="section-stat card border-0 shadow-sm h-100 section-stat-accent">
                    <div class="card-body">
                        <div class="section-stat-label"><?= __('Section Participants') ?></div>
                        <div class="section-stat-value"><?= $this->Number->format($participantCount) ?></div>
                        <div class="section-stat-note"><?= __('Participants assigned to sections in this group') ?></div>
                    </div>
                </article>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8" id="sections">
                <div class="mb-4" id="checkpoint-progress">
                    <?= $this->element('Dashboard/checkpoint_progress_chart', [
                        'progress' => $checkpointProgress,
                        'title' => __('Checkpoint Progress'),
                        'description' => __('Track the highest checkpoint reached by participants assigned to sections in this group.'),
                        'emptyMessage' => __('No checkpoint progress has been recorded for this group yet.'),
                    ]) ?>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Sections') ?></span>
                        <span class="badge text-bg-info border"><?= $this->Number->format($sectionCount) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div class="small text-secondary">
                                <?php if ($showAll) : ?>
                                    <?= __('Showing teams from all events across this group.') ?>
                                <?php elseif ($currentEvent !== null) : ?>
                                    <?= __('Showing teams for the current event: {0}', h($currentEvent->event_name)) ?>
                                <?php else : ?>
                                    <?= __('No current event is active, so teams from all events are shown.') ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($showAll) : ?>
                                    <?= $this->Html->link(__('Show Current Event Only'), ['action' => 'view', $group->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                                <?php else : ?>
                                    <?= $this->Html->link(__('Show All Events'), ['action' => 'view', $group->id, '?' => ['all' => '1']], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($sectionSummaries !== []) : ?>
                            <div class="section-team-stack">
                                <?php foreach ($sectionSummaries as $summary) : ?>
                                    <?php $section = $summary['section']; ?>
                                    <article class="section-team-card">
                                        <div class="section-team-card__header">
                                            <div>
                                                <div class="section-team-card__eyebrow">
                                                    <?= $section->hasValue('participant_type') ? h($section->participant_type->participant_type) : __('Section') ?>
                                                </div>
                                                <h3 class="h4 mb-1"><?= h($section->section_name) ?></h3>
                                                <div class="section-team-card__meta">
                                                    <span><?= __('{0} teams', $this->Number->format($summary['team_count'])) ?></span>
                                                    <span class="section-meta-separator">/</span>
                                                    <span><?= __('{0} participants', $this->Number->format($summary['section_participant_count'])) ?></span>
                                                    <?php if ($section->osm_section_id !== null) : ?>
                                                        <span class="section-meta-separator">/</span>
                                                        <span><?= __('OSM {0}', $this->Number->format($section->osm_section_id)) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="actions text-nowrap">
                                                <?= $this->Actions->buttons($section, ['outline' => true]) ?>
                                            </div>
                                        </div>

                                        <?php if ($summary['teams'] !== []) : ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th><?= __('Team') ?></th>
                                                            <th><?= __('Event') ?></th>
                                                            <th><?= __('In Section') ?></th>
                                                            <th><?= __('Team Size') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($summary['teams'] as $team) : ?>
                                                            <tr>
                                                                <td class="fw-semibold">
                                                                    <?= $this->Html->link($team['entry']->entry_name, ['controller' => 'Entries', 'action' => 'view', $team['entry']->id]) ?>
                                                                </td>
                                                                <td><?= h($team['entry']->event?->event_name ?? __('Unknown event')) ?></td>
                                                                <td>
                                                                    <?= implode(', ', array_map(
                                                                        static fn($participant): string => trim($participant->full_name),
                                                                        $team['section_participants'],
                                                                    )) ?>
                                                                </td>
                                                                <td><?= $this->Number->format($team['participant_count']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else : ?>
                                            <div class="text-secondary"><?= __('No teams are signed up for this section yet.') ?></div>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-secondary"><?= __('This group has no sections yet.') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card shadow-sm mb-4" id="details">
                    <div class="card-header"><?= __('Group Details') ?></div>
                    <div class="card-body">
                        <div class="section-detail-grid">
                            <div>
                                <div class="section-detail-label"><?= __('Group Name') ?></div>
                                <div class="section-detail-value"><?= h($group->group_name) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Visible') ?></div>
                                <div class="section-detail-value"><?= $group->visible ? __('Yes') : __('No') ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Sort Order') ?></div>
                                <div class="section-detail-value"><?= $this->Number->format($group->sort_order) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Created') ?></div>
                                <div class="section-detail-value"><?= h($group->created) ?></div>
                            </div>
                            <div>
                                <div class="section-detail-label"><?= __('Modified') ?></div>
                                <div class="section-detail-value"><?= h($group->modified) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header"><?= __('Quick Actions') ?></div>
                    <div class="card-body d-grid gap-2">
                        <?= $this->Html->link(__('Edit Group'), ['action' => 'edit', $group->id], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?= $this->Html->link(__('Add Section'), ['controller' => 'Sections', 'action' => 'add', '?' => ['group_id' => $group->id]], ['class' => 'btn btn-outline-primary text-start']) ?>
                        <?= $this->Html->link(__('View All Groups'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary text-start']) ?>
                        <?= $this->Form->postLink(__('Delete Group'), ['action' => 'delete', $group->id], [
                            'confirm' => __('Are you sure you want to delete {0}?', $group->group_name),
                            'class' => 'btn btn-outline-danger text-start',
                        ]) ?>
                    </div>
                </div>

                <?php if (isset($billing)) : ?>
                    <div class="card shadow-sm" id="billing">
                        <div class="card-header"><?= __('Billing Snapshot') ?></div>
                        <div class="card-body">
                            <?php if ($billing->count() > 0) : ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($billing as $billingRow) : ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="fw-semibold"><?= h($billingRow->section->section_name ?? __('Section')) ?></div>
                                                    <div class="small text-secondary"><?= __('Uniformed members checked in') ?></div>
                                                </div>
                                                <span class="badge text-bg-info border"><?= $this->Number->format((int)$billingRow->uniformed_members) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="text-secondary"><?= __('No billing rows matched the current event filter.') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
