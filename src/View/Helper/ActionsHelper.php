<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * @extends \Cake\View\Helper<\App\View\AppView>
 * @property \BootstrapUI\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 */
class ActionsHelper extends Helper
{
    /**
     * @var array<int, string>
     */
    protected array $helpers = ['Html', 'Form'];

    /**
     * Renders a Bootstrap 5 action button group
     *
     * @param \Cake\Datasource\EntityInterface $entity
     * @param array<string, mixed> $options Optional customization for button classes, icons, visibility
     * @return string
     */
    public function buttons(EntityInterface $entity, array $options = []): string
    {
        $id = $entity->get('id');

        // Extract controller name from entity class
        $modelAlias = $entity->getSource();
        $controller = Inflector::camelize($modelAlias);

        $defaults = [
            'view' => [
                'class' => 'btn btn-sm btn-success',
                'icon' => 'eye',
                'show' => true,
            ],
            'edit' => [
                'class' => 'btn btn-sm btn-warning',
                'icon' => 'pencil-square',
                'show' => true,
            ],
            'delete' => [
                'class' => 'btn btn-sm btn-danger',
                'icon' => 'trash3',
                'show' => true,
                'confirm' => __('Are you sure you want to delete # {0}?', $id),
            ],
        ];

        $config = array_replace_recursive($defaults, $options);
        $deleted = (bool)($options['deleted'] ?? false);

        $outline = $options['outline'] ?? false;

        if ($outline) {
            foreach (['view', 'edit', 'delete'] as $action) {
                $config[$action]['class'] = preg_replace(
                    '/btn-(sm )?(success|warning|danger)/',
                    'btn-$1outline-$2',
                    $config[$action]['class'],
                );
            }
        }

        if ($deleted) {
            $config['delete']['class'] = str_replace('danger', 'info', $config['delete']['class']);
            $config['delete']['icon'] = 'arrow-counterclockwise';
            $config['delete']['confirm'] = __('Are you sure you want to restore # {0}?', $id);
        }

        $html = '<div class="btn-group" role="group">';

        if ($config['view']['show']) {
            $html .= $this->Html->link(
                $this->Html->icon($config['view']['icon']),
                ['controller' => $controller, 'action' => 'view', $id],
                ['escape' => false, 'class' => $config['view']['class']],
            );
        }

        if ($config['edit']['show']) {
            $html .= $this->Html->link(
                $this->Html->icon($config['edit']['icon']),
                ['controller' => $controller, 'action' => 'edit', $id],
                ['escape' => false, 'class' => $config['edit']['class']],
            );
        }

        if ($config['delete']['show']) {
            $html .= $this->Form->postLink(
                $this->Html->icon($config['delete']['icon']),
                ['controller' => $controller, 'action' => $deleted ? 'restore' : 'delete', $id],
                [
                    'escape' => false,
                    'class' => $config['delete']['class'],
                    'confirm' => $config['delete']['confirm'],
                ],
            );
        }

        $html .= '</div>';

        return $html;
    }
}
