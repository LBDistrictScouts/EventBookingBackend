<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 * @var iterable<\App\Model\Entity\Section> $sections
 * @var array<string, int|string> $sectionsPagination
 */
?>
<?php
$query = $this->request->getQueryParams();
$currentSort = (string)$sectionsPagination['sort'];
$currentDirection = (string)$sectionsPagination['direction'];
$page = (int)$sectionsPagination['page'];
$pageCount = (int)$sectionsPagination['page_count'];
$total = (int)$sectionsPagination['total'];
$pageParam = (string)$sectionsPagination['page_param'];
$sortParam = (string)$sectionsPagination['sort_param'];
$directionParam = (string)$sectionsPagination['direction_param'];
$anchor = (string)$sectionsPagination['anchor'];

$buildUrl = function (array $overrides) use ($event, $query, $anchor): array {
    return [
        'controller' => 'Events',
        'action' => 'view',
        $event->id,
        '?' => array_merge($query, $overrides),
        '#' => $anchor,
    ];
};

$sortLink = function (string $field, string $label) use (
    $buildUrl,
    $currentSort,
    $currentDirection,
    $sortParam,
    $directionParam,
    $pageParam,
): string {
    $nextDirection = $currentSort === $field && $currentDirection === 'asc' ? 'desc' : 'asc';
    $isActive = $currentSort === $field;
    $classes = 'link-body-emphasis text-decoration-none table-sort-link';
    if ($isActive) {
        $classes .= ' active';
    }

    return $this->Html->link(
        $label,
        $buildUrl([
            $sortParam => $field,
            $directionParam => $nextDirection,
            $pageParam => 1,
        ]),
        [
            'class' => $classes,
            'data-ajax-table-link' => 'sections',
            'data-ajax-target' => 'sections-table',
        ],
    );
};
?>
<div class="card shadow-sm ajax-table-card" id="sections-table" data-ajax-table-container="sections">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= __('Sections') ?></span>
        <span class="badge text-bg-info border"><?= $this->Number->format($total) ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (count($sections) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= $sortLink('section_name', __('Section')) ?></th>
                            <th><?= $sortLink('group_name', __('Group')) ?></th>
                            <th><?= $sortLink('participant_type', __('Participant Type')) ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section) : ?>
                            <tr>
                                <td class="fw-semibold"><?= h($section->section_name) ?></td>
                                <td><?= $section->has('group') ? h($section->group->group_name) : '' ?></td>
                                <td><?= $section->has('participant_type') ?
                                        h($section->participant_type->participant_type)
                                        : '' ?></td>
                                <td class="actions"><?= $this->Actions->buttons($section, ['outline' => true]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pageCount > 1) : ?>
                <div class="card-footer bg-body-tertiary border-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="small text-secondary">
                            <?= __('Page {0} of {1}', $this->Number->format($page), $this->Number->format($pageCount))
                            ?>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="<?= __('Sections pagination') ?>">
                            <?=
                            $this->Html->link(
                                __('Previous'),
                                $buildUrl([$pageParam => max(1, $page - 1)]),
                                [
                                    'class' => 'btn btn-outline-secondary' . ($page <= 1 ? ' disabled' : ''),
                                    'data-ajax-table-link' => 'sections',
                                    'data-ajax-target' => 'sections-table',
                                ],
                            )
                            ?>
                            <?=
                            $this->Html->link(
                                __('Next'),
                                $buildUrl([$pageParam => min($pageCount, $page + 1)]),
                                [
                                    'class' => 'btn btn-outline-secondary' . ($page >= $pageCount ? ' disabled' : ''),
                                    'data-ajax-table-link' => 'sections',
                                    'data-ajax-target' => 'sections-table',
                                ],
                            )
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="p-4 text-secondary"><?= __('No sections have been linked to this event yet.') ?></div>
        <?php endif; ?>
    </div>
</div>
