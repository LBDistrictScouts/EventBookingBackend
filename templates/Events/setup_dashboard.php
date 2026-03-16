<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array<string, mixed>> $setupSections
 */
?>
<div class="events setup-dashboard container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">
            <div class="mb-4">
                <h2>No active event is configured yet</h2>
                <p class="text-muted">
                    The application is running, but there is no current bookable event in the database.
                    Use the links below to enter the base data needed for a new event.
                </p>
            </div>

            <div class="row g-4">
                <?php foreach ($setupSections as $section) : ?>
                    <div class="col-12 col-xl-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="h5 card-title"><?= h($section['title']) ?></h3>
                                <p class="card-text text-muted"><?= h($section['description']) ?></p>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($section['links'] as $link) : ?>
                                        <?= $this->Html->link(
                                            $link['label'],
                                            $link['url'],
                                            ['class' => 'btn btn-outline-primary btn-sm'],
                                        ) ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
