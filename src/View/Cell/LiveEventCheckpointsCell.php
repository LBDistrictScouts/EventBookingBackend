<?php
declare(strict_types=1);

namespace App\View\Cell;

use App\Model\Entity\Event;
use Cake\Cache\Cache;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\Cell;

/**
 * @extends \Cake\View\Cell<\App\View\AppView>
 */
class LiveEventCheckpointsCell extends Cell
{
    use LocatorAwareTrait;

    public const CACHE_KEY = 'live_event_checkpoints_sidebar_v1';

    /**
     * Render the cached live event sidebar branches.
     *
     * @return void
     */
    public function display(): void
    {
        /** @var array{
         *     event: array{id: string, name: string}|null,
         *     checkpoints: list<array{id: string, label: string}>
         * } $navigation
         */
        $navigation = Cache::remember(self::CACHE_KEY, function (): array {
            /** @var \App\Model\Table\EventsTable $eventsTable */
            $eventsTable = $this->getTableLocator()->get('Events');
            /** @var \App\Model\Entity\Event|null $currentEvent */
            $currentEvent = $eventsTable->find()
                ->contain(['Checkpoints' => ['sort' => ['Checkpoints.checkpoint_sequence' => 'ASC']]])
                ->where(['bookable' => true, 'finished' => false])
                ->orderByAsc('start_time')
                ->first();

            if (!$currentEvent instanceof Event) {
                return [
                    'event' => null,
                    'checkpoints' => [],
                ];
            }

            $checkpoints = [];
            foreach ($currentEvent->checkpoints as $checkpoint) {
                $checkpoints[] = [
                    'id' => (string)$checkpoint->id,
                    'label' => sprintf(
                        '%d. %s',
                        (int)$checkpoint->checkpoint_sequence,
                        (string)$checkpoint->checkpoint_name,
                    ),
                ];
            }

            return [
                'event' => [
                    'id' => (string)$currentEvent->id,
                    'name' => (string)$currentEvent->event_name,
                ],
                'checkpoints' => $checkpoints,
            ];
        }, 'navigation');

        $currentPass = (array)$this->request->getParam('pass');
        $currentController = (string)$this->request->getParam('controller');
        $currentAction = (string)$this->request->getParam('action');

        $this->set(compact('navigation', 'currentPass', 'currentController', 'currentAction'));
    }
}
