<?php
/**
 * @var \App\View\AppView $this
 */

$currentController = (string)$this->request->getParam('controller');
$currentAction = (string)$this->request->getParam('action');
$currentPass = (array)$this->request->getParam('pass');
$currentId = (string)($currentPass[0] ?? '');

$tree = [
    [
        'title' => __('Setup'),
        'controllers' => ['Groups', 'Sections', 'ParticipantTypes', 'Questions'],
        'links' => [
            ['label' => __('Groups'), 'url' => ['controller' => 'Groups', 'action' => 'index'], 'controller' => 'Groups'],
            ['label' => __('Sections'), 'url' => ['controller' => 'Sections', 'action' => 'index'], 'controller' => 'Sections'],
            [
                'label' => __('Participant Types'),
                'url' => ['controller' => 'ParticipantTypes', 'action' => 'index'],
                'controller' => 'ParticipantTypes',
            ],
            ['label' => __('Questions'), 'url' => ['controller' => 'Questions', 'action' => 'index'], 'controller' => 'Questions'],
        ],
    ],
];
?>
<div class="app-nav-tree px-3">
    <?= $this->cell('LiveEventCheckpoints') ?>
    <?php foreach ($tree as $branch) : ?>
        <?php $isBranchActive = in_array($currentController, $branch['controllers'], true); ?>
        <details class="app-nav-branch"<?= $isBranchActive ? ' open' : '' ?>>
            <summary class="app-nav-branch__summary">
                <span><?= h($branch['title']) ?></span>
            </summary>
            <ul class="nav flex-column app-nav-branch__list">
                <?php foreach ($branch['links'] as $link) : ?>
                    <?php
                    $isLinkActive = $currentController === $link['controller'] &&
                        (!isset($link['action']) || $currentAction === $link['action']) &&
                        (!isset($link['checkpoint_id']) || $currentId === $link['checkpoint_id']);
                    ?>
                    <li class="nav-item">
                        <?= $this->Html->link(
                            $link['label'],
                            $link['url'],
                            ['class' => 'nav-link app-nav-link' . ($isLinkActive ? ' active' : '')],
                        ) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </details>
    <?php endforeach; ?>
</div>
