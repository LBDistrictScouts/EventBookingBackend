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

$this->Html->css('BootstrapUI.dashboard', ['block' => true]);
$this->prepend(
    name: 'tb_body_attrs',
    value: ' class="' .
        implode(' ', [h($this->request->getParam('controller')), h($this->request->getParam('action'))]) .
        '" ',
);
$this->start('tb_body_start');
?>
<body <?= $this->fetch('tb_body_attrs') ?>>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <?= $this->Html->link(
            title: Configure::read('App.title', 'LBD Event Booking'),
            url: '/',
            options: ['class' => 'navbar-brand col-md-3 col-lg-2 me-0 px-3'],
        ) ?>
        <button
            class="navbar-toggler position-absolute d-md-none collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
        <ul class="navbar-nav px-3 w-100">
            <li class="nav-item text-nowrap">
                <?= $this->Html->link('Home', '/', ['class' => 'nav-link']) ?>
            </li>
            <li class="nav-item text-nowrap">
                <?= $this->Html->link(
                    'Groups',
                    ['controller' => 'Groups', 'action' => 'index'],
                    ['class' => 'nav-link'],
                ) ?>
            </li>
            <li class="nav-item text-nowrap">
                <?= $this->Html->link(
                    'Events',
                    ['controller' => 'Events', 'action' => 'index'],
                    ['class' => 'nav-link'],
                ) ?>
            </li>
            <li class="nav-item text-nowrap">
                <?= $this->Html->link(
                    'Entries',
                    ['controller' => 'Entries', 'action' => 'index'],
                    ['class' => 'nav-link'],
                ) ?>
            </li>
        </ul>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style="">
                <div class="position-sticky pt-3">
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
