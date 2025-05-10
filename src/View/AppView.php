<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use BootstrapUI\View\UIView;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @property \App\View\Helper\ActionsHelper $Actions
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends UIView
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like adding helpers.
     *
     * e.g. `$this->addHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->addHelper('Actions');
        $this->addHelper('Paginator');

        $this->Paginator->setTemplates([
            'sort' => '<a href="{{url}}" class="text-decoration-none text-body">{{text}}</a>',
            'sortAsc' => '<a href="{{url}}" class="text-decoration-none text-body">{{text}} ▲</a>',
            'sortDesc' => '<a href="{{url}}" class="text-decoration-none text-body">{{text}} ▼</a>',

            // Pagination controls
            'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}" title="Next page">{{text}}</a></li>',
            'nextDisabled' => '<li class="page-item disabled"><span class="page-link">{{text}}</span></li>',

            'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}" title="Previous page">{{text}}</a></li>',
            'prevDisabled' => '<li class="page-item disabled"><span class="page-link">{{text}}</span></li>',

            'first' => '<li class="page-item"><a class="page-link" href="{{url}}" title="First page">{{text}}</a></li>',
            'last' => '<li class="page-item"><a class="page-link" href="{{url}}" title="Last page">{{text}}</a></li>',

            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active"><span class="page-link">{{text}}</span></li>',

            // Optional wrappers for the pagination block
            'counterRange' => '{{start}} - {{end}} of {{count}}',
            'counterPages' => '{{page}} of {{pages}}',
        ]);
    }
}
