<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;

foreach ((array)Configure::read('Theme.stylesheets', []) as $stylesheet) {
    $this->Html->css($stylesheet, ['block' => true]);
}
$this->Html->css('app-shell', ['block' => true]);
foreach ((array)Configure::read('Theme.scripts', []) as $script) {
    $this->Html->script($script, ['block' => true]);
}
$this->prepend(
    name: 'tb_body_attrs',
    value: ' class="' .
        implode(' ', [h($this->request->getParam('controller')), h($this->request->getParam('action'))]) .
        '" ',
);
$this->start('tb_body_start');
?>
<body <?= $this->fetch('tb_body_attrs') ?>>
    <?php
    $currentController = (string)$this->request->getParam('controller');
    $topLinks = [
        ['label' => 'Home', 'url' => '/', 'controller' => 'Events', 'action' => 'current'],
        ['label' => 'Events', 'url' => ['controller' => 'Events', 'action' => 'index'], 'controller' => 'Events'],
        ['label' => 'Entries', 'url' => ['controller' => 'Entries', 'action' => 'index'], 'controller' => 'Entries'],
        ['label' => 'Checkpoints', 'url' => ['controller' => 'Checkpoints', 'action' => 'index'], 'controller' => 'Checkpoints'],
    ];
    ?>
    <header class="navbar navbar-expand-md navbar-dark sticky-top app-topbar shadow-sm">
        <div class="container-fluid">
            <?= $this->Html->link(
                title: Configure::read('App.title', 'LBD Event Booking'),
                url: '/',
                options: ['class' => 'navbar-brand'],
            ) ?>
            <div class="collapse navbar-collapse d-none d-md-flex" id="appTopbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                    <?php foreach ($topLinks as $link) : ?>
                        <li class="nav-item">
                            <?= $this->Html->link(
                                $link['label'],
                                $link['url'],
                                [
                                    'class' => 'nav-link' .
                                        ($currentController === ($link['controller'] ?? '') &&
                                        (($link['action'] ?? null) === null ||
                                         (string)$this->request->getParam('action') === $link['action'])
                                            ? ' active'
                                            : ''),
                                ],
                            ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button
                class="navbar-toggler d-md-none collapsed" type="button"
                data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar app-sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="px-3 mb-3">
                        <?= $this->Form->create(null, [
                            'type' => 'get',
                            'url' => ['controller' => 'Entries', 'action' => 'findByReference'],
                            'class' => 'sidebar-entry-search',
                        ]) ?>
                        <label for="sidebar-entry-reference" class="form-label small text-uppercase fw-semibold mb-1">
                            <?= __('Entry Reference') ?>
                        </label>
                        <div class="input-group input-group-sm">
                            <?= $this->Form->control('reference', [
                                'label' => false,
                                'id' => 'sidebar-entry-reference',
                                'placeholder' => 'ABC-123',
                                'class' => 'form-control',
                                'templates' => ['inputContainer' => '{{content}}'],
                            ]) ?>
                            <?= $this->Form->button(__('Go'), ['class' => 'btn btn-primary']) ?>
                        </div>
                        <div class="form-text"><?= __('Use full ref or number only.') ?></div>
                        <?= $this->Form->end() ?>
                    </div>
                    <ul class="navbar-nav px-3 w-100 d-md-none">
                        <li class="nav-item text-nowrap">
                            <?= $this->Html->link('Home', '/', ['class' => 'nav-link']) ?>
                        </li>
                        <li class="nav-item text-nowrap">
                            <?= $this->Html->link('Events', ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?>
                        </li>
                        <li class="nav-item text-nowrap">
                            <?= $this->Html->link('Entries', ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?>
                        </li>
                        <li class="nav-item text-nowrap">
                            <?= $this->Html->link('Checkpoints', ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?>
                        </li>
                    </ul>
                    <hr class="d-md-none"/>
                    <?= $this->fetch('tb_sidebar') ?>
                </div>
            </nav>

            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center
                            pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 page-header"><?= h($this->request->getParam('controller')) ?></h1>
                </div>
<?php
/**
 * Default `flash` block.
 */
if (!$this->fetch('tb_flash')) {
    $this->start('tb_flash');
    if (isset($this->Flash)) {
        echo $this->Flash->render();
    }
    $this->end();
}
$this->end();

$this->start('tb_body_end');
?>
            </main>
        </div>
    </div>
</body>
<?php
$this->end();

echo $this->fetch('content');
