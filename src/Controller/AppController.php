<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Exception;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        try {
            $this->loadComponent('Authentication.Authentication');

            $this->loadComponent('Flash');

            /*
             * Enable the following component for recommended CakePHP form protection settings.
             * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
             */
//            $this->loadComponent('FormProtection');
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @param \Cake\Event\EventInterface<static> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $session = $this->request->getSession();
        $expiresAt = $session->read('Auth.expires_at');

        if ($expiresAt && time() > $expiresAt) {
            $session->destroy();
            $this->Flash->error('Your session has expired. Please log in again.');
        }

        // 🔹 Allow DebugKit requests to bypass authentication
        if ($this->request->getParam('plugin') === 'DebugKit') {
            /** @var \Authentication\Controller\Component\AuthenticationComponent $authentication */
            $authentication = $this->Authentication;
            $action = $this->request->getParam('action');
            if (is_string($action)) {
                $authentication->allowUnauthenticated([$action]);
            }
        }
    }

    /**
     * Build consistent entry selector labels: BOOKINGCODE-REF [count] Name.
     *
     * @param \Cake\ORM\Table|\Cake\ORM\Association $entriesTable Entries table or association.
     * @param string|null $eventId Optional event filter.
     * @param int $limit Result limit.
     * @return array<int|string, string>
     */
    protected function buildEntryOptions(
        Table|Association $entriesTable,
        ?string $eventId = null,
        int $limit = 200,
    ): array {
        $table = $entriesTable instanceof Association ? $entriesTable->getTarget() : $entriesTable;

        $query = $table->find(
            'list',
            keyField: 'id',
            valueField: function ($entry): string {
                return sprintf(
                    '%s-%d [%d] %s',
                    $entry->event->booking_code,
                    $entry->reference_number,
                    $entry->participant_count,
                    $entry->entry_name,
                );
            },
        )
            ->contain(['Events'])
            ->orderByAsc('Entries.reference_number')
            ->limit($limit);

        if ($eventId !== null && $eventId !== '') {
            $query->where(['Entries.event_id' => $eventId]);
        }

        /** @var array<int|string, string> $entryOptions */
        $entryOptions = $query->toArray();

        return $entryOptions;
    }
}
