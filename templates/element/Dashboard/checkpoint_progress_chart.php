<?php
/**
 * @var \App\View\AppView $this
 * @var array{
 *     bars: list<array{sequence: int, sequence_label: string, label: string, count: int, width: float}>,
 *     participant_count: int,
 *     tracked_participant_count: int,
 *     max_count: int
 * } $progress
 * @var string $title
 * @var string $description
 * @var string $emptyMessage
 */
?>
<div class="card shadow-sm checkpoint-progress-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= h($title) ?></span>
        <span class="badge text-bg-info border">
            <?= __('{0} tracked', $this->Number->format($progress['tracked_participant_count'])) ?>
        </span>
    </div>
    <div class="card-body">
        <p class="text-secondary mb-4"><?= h($description) ?></p>

        <?php if ($progress['bars'] !== []) : ?>
            <div class="checkpoint-progress-chart" role="img" aria-label="<?= h($title) ?>">
                <?php foreach ($progress['bars'] as $bar) : ?>
                    <div class="checkpoint-progress-row">
                        <div class="checkpoint-progress-labels">
                            <div class="checkpoint-progress-sequence"><?= h($bar['sequence_label']) ?></div>
                            <div class="checkpoint-progress-name"><?= h($bar['label']) ?></div>
                        </div>
                        <div class="checkpoint-progress-bar-wrap">
                            <div class="checkpoint-progress-bar-track">
                                <div
                                    class="checkpoint-progress-bar-fill"
                                    style="width: <?= $this->Number->precision($bar['width'], 2) ?>%;"
                                ></div>
                            </div>
                            <div class="checkpoint-progress-count">
                                <?= __('{0} participants', $this->Number->format($bar['count'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-secondary"><?= h($emptyMessage) ?></div>
        <?php endif; ?>
    </div>
</div>
