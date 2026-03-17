<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * @extends \Cake\View\Helper<\App\View\AppView>
 * @property \BootstrapUI\View\Helper\HtmlHelper $Html
 */
class BooleanIconHelper extends Helper
{
    /**
     * @var array<int, string>
     */
    protected array $helpers = ['Html'];

    /**
     * Render an icon when the given value is truthy.
     *
     * @param mixed $value The value to evaluate.
     * @param string $icon Bootstrap icon name.
     * @param array<string, mixed> $options HTML options passed to the icon helper.
     * @return string
     */
    public function render(mixed $value, string $icon = 'check-circle', array $options = []): string
    {
        if (!$value) {
            return '';
        }

        return $this->Html->icon($icon, $options);
    }
}
