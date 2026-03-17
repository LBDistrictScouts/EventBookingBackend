<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 * @var iterable<\App\Model\Entity\Entry> $entries
 * @var array<string, int|string> $entriesPagination
 * @var string $entriesSearch
 */
?>
<?php
$query = $this->request->getQueryParams();
$currentSort = (string)$entriesPagination['sort'];
$currentDirection = (string)$entriesPagination['direction'];
$page = (int)$entriesPagination['page'];
$pageCount = (int)$entriesPagination['page_count'];
$total = (int)$entriesPagination['total'];
$pageParam = (string)$entriesPagination['page_param'];
$sortParam = (string)$entriesPagination['sort_param'];
$directionParam = (string)$entriesPagination['direction_param'];
$anchor = (string)$entriesPagination['anchor'];

$buildUrl = function (array $overrides) use ($event, $query, $anchor): array {
    $mergedQuery = array_merge($query, $overrides);
    $mergedQuery = array_filter(
        $mergedQuery,
        static fn ($value): bool => $value !== null && $value !== '',
    );

    return [
        'controller' => 'Events',
        'action' => 'view',
        $event->id,
        '?' => $mergedQuery,
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
            'data-ajax-table-link' => 'entries',
            'data-ajax-target' => 'entries-table',
        ],
    );
};
?>
<div class="card shadow-sm ajax-table-card" id="entries-table" data-ajax-table-container="entries">
    <div class="card-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span><?= __('Entries') ?></span>
                <span class="badge text-bg-info border"><?= $this->Number->format($total) ?></span>
            </div>
            <?= $this->Form->create(null, [
                'type' => 'get',
                'url' => $this->Url->build($buildUrl([$pageParam => 1])),
                'class' => 'event-table-search',
                'valueSources' => 'query',
                'data-ajax-table-form' => 'entries',
                'data-ajax-target' => 'entries-table',
            ]) ?>
            <div class="input-group input-group-sm event-table-search-group">
                <?= $this->Form->control('entries_search', [
                    'label' => false,
                    'value' => $entriesSearch,
                    'placeholder' => __('Search name, email or mobile'),
                    'class' => 'form-control',
                    'templates' => ['inputContainer' => '{{content}}'],
                    'data-ajax-search-input' => 'entries',
                ]) ?>
                <?php foreach ($query as $key => $value) : ?>
                    <?php if ($key === 'entries_search' || $key === $pageParam) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php if (is_scalar($value)) : ?>
                        <?= $this->Form->hidden($key, ['value' => (string)$value]) ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?= $this->Form->button(__('Search'), ['class' => 'btn btn-outline-primary']) ?>
                <?php if ($entriesSearch !== '') : ?>
                    <?= $this->Html->link(
                        __('Clear'),
                        $buildUrl(['entries_search' => null, $pageParam => 1]),
                        [
                            'class' => 'btn btn-outline-secondary',
                            'data-ajax-table-link' => 'entries',
                            'data-ajax-target' => 'entries-table',
                        ],
                    ) ?>
                <?php endif; ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (count($entries) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= $sortLink('reference_number', __('Reference')) ?></th>
                            <th><?= $sortLink('entry_name', __('Entry')) ?></th>
                            <th><?= __('Contact') ?></th>
                            <th><?= $sortLink('participant_count', __('Participants')) ?></th>
                            <th><?= $sortLink('checked_in_count', __('Checked In')) ?></th>
                            <th><?= __('Check Ins') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry) : ?>
                            <?php
                            $reference = $event->booking_code . '-' . $this->Number->format($entry->reference_number);
                            $entryCheckIns = count($entry->check_ins ?? []);
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= h($reference) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= h($entry->entry_name) ?></div>
                                    <div class="small text-secondary"><?= $entry->active ? __('Active') : __('Inactive') ?></div>
                                </td>
                                <td>
                                    <div><?= $this->Text->autoLinkEmails($entry->entry_email) ?></div>
                                    <div class="small text-secondary"><?= h($entry->entry_mobile ?: __('No mobile')) ?></div>
                                </td>
                                <td><?= $this->Number->format($entry->participant_count) ?></td>
                                <td><?= $this->Number->format($entry->checked_in_count) ?></td>
                                <td><?= $this->Number->format($entryCheckIns) ?></td>
                                <td class="actions"><?= $this->Actions->buttons($entry, ['outline' => true]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pageCount > 1) : ?>
                <div class="card-footer bg-body-tertiary border-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="small text-secondary">
                            <?= __('Page {0} of {1}', $this->Number->format($page), $this->Number->format($pageCount)) ?>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="<?= __('Entries pagination') ?>">
                            <?=
                            $this->Html->link(
                                __('Previous'),
                                $buildUrl([$pageParam => max(1, $page - 1)]),
                                [
                                    'class' => 'btn btn-outline-secondary' . ($page <= 1 ? ' disabled' : ''),
                                    'data-ajax-table-link' => 'entries',
                                    'data-ajax-target' => 'entries-table',
                                ],
                            )
                            ?>
                            <?=
                            $this->Html->link(
                                __('Next'),
                                $buildUrl([$pageParam => min($pageCount, $page + 1)]),
                                [
                                    'class' => 'btn btn-outline-secondary' . ($page >= $pageCount ? ' disabled' : ''),
                                    'data-ajax-table-link' => 'entries',
                                    'data-ajax-target' => 'entries-table',
                                ],
                            )
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="p-4 text-secondary">
                <?= $entriesSearch !== ''
                    ? __('No entries matched that search.')
                    : __('No entries have been added to this event yet.') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
