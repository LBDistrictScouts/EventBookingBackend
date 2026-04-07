<?php
/**
 * @var \App\View\AppView $this
 * @var array{
 *     event: array{id: string, name: string}|null,
 *     checkpoints: list<array{id: string, label: string}>
 * } $navigation
 * @var list<mixed> $currentPass
 * @var string $currentController
 * @var string $currentAction
 */
?>
<?php $currentId = (string)($currentPass[0] ?? ''); ?>
<?php $currentEvent = $navigation['event']; ?>
<details class="app-nav-branch"<?= in_array($currentController, ['Events', 'Entries'], true) ? ' open' : '' ?>>
    <summary class="app-nav-branch__summary">
        <span><?= __('Live Event') ?></span>
    </summary>
    <ul class="nav flex-column app-nav-branch__list">
        <?php if ($currentEvent !== null) : ?>
            <li class="nav-item">
                <?= $this->Html->link(
                    $currentEvent['name'],
                    ['controller' => 'Events', 'action' => 'view', $currentEvent['id']],
                    [
                        'class' => 'nav-link app-nav-link' .
                            ($currentController === 'Events' &&
                            $currentAction === 'view' &&
                            $currentId === $currentEvent['id']
                                ? ' active'
                                : ''),
                    ],
                ) ?>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <?= $this->Html->link(
                __('Entries'),
                ['controller' => 'Entries', 'action' => 'index'],
                ['class' => 'nav-link app-nav-link' . ($currentController === 'Entries' ? ' active' : '')],
            ) ?>
        </li>
        <li class="nav-item">
            <?= $this->Html->link(
                __('Participants'),
                ['controller' => 'Participants', 'action' => 'index'],
                ['class' => 'nav-link app-nav-link' . ($currentController === 'Participants' ? ' active' : '')],
            ) ?>
        </li>
    </ul>
</details>

<?php if ($navigation['checkpoints'] !== []) : ?>
    <details class="app-nav-branch"<?= $currentController === 'Checkpoints' && $currentAction === 'view' ? ' open' : '' ?>>
        <summary class="app-nav-branch__summary">
            <span><?= __('Event Checkpoints') ?></span>
        </summary>
        <ul class="nav flex-column app-nav-branch__list">
            <?php foreach ($navigation['checkpoints'] as $checkpoint) : ?>
                <li class="nav-item">
                    <?= $this->Html->link(
                        $checkpoint['label'],
                        ['controller' => 'Checkpoints', 'action' => 'view', $checkpoint['id']],
                        [
                            'class' => 'nav-link app-nav-link' .
                                ($currentController === 'Checkpoints' &&
                                $currentAction === 'view' &&
                                $currentId === $checkpoint['id']
                                    ? ' active'
                                    : ''),
                        ],
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </details>
<?php endif; ?>
