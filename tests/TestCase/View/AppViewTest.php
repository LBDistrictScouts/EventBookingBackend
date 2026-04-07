<?php
declare(strict_types=1);

namespace App\Test\TestCase\View;

use App\View\AppView;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

/**
 * @uses \App\View\AppView
 */
class AppViewTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Groups',
        'app.ParticipantTypes',
        'app.Sections',
        'app.Events',
        'app.EventsSections',
        'app.Checkpoints',
        'app.Entries',
        'app.Participants',
        'app.CheckIns',
        'app.ParticipantsCheckIns',
        'app.Questions',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Router::reload();
        $routes = require dirname(__DIR__, 3) . '/config/routes.php';
        $routes(Router::createRouteBuilder('/'));
    }

    public function testFlashHelperIsLoaded(): void
    {
        $view = $this->createView();

        $this->assertTrue($view->helpers()->has('Flash'));
    }

    public function testDashboardLayoutRendersBootstrapFlashFromSession(): void
    {
        $view = $this->createView([
            'Flash' => [
                'flash' => [
                    [
                        'message' => 'Dashboard flash test',
                        'key' => 'flash',
                        'element' => 'flash/success',
                        'params' => [],
                    ],
                ],
            ],
        ]);

        $output = (string)$view->Flash->render();

        $this->assertStringContainsString('Dashboard flash test', $output);
        $this->assertStringContainsString('alert alert-success alert-dismissible fade show', $output);
    }

    public function testFlashIsConsumedAfterFirstDashboardRender(): void
    {
        $view = $this->createView([
            'Flash' => [
                'flash' => [
                    [
                        'message' => 'Consumed flash test',
                        'key' => 'flash',
                        'element' => 'flash/error',
                        'params' => [],
                    ],
                ],
            ],
        ]);

        $firstOutput = (string)$view->Flash->render();
        $secondOutput = (string)$view->Flash->render();

        $this->assertStringContainsString('Consumed flash test', $firstOutput);
        $this->assertStringContainsString('alert alert-danger alert-dismissible fade show', $firstOutput);
        $this->assertStringNotContainsString('Consumed flash test', $secondOutput);
    }

    /**
     * @param array<string, mixed> $sessionData
     * @return \App\View\AppView
     */
    private function createView(array $sessionData = []): AppView
    {
        $session = Session::create();
        foreach ($sessionData as $key => $value) {
            $session->write($key, $value);
        }

        $request = new ServerRequest([
            'params' => [
                'controller' => 'Entries',
                'action' => 'view',
                'plugin' => null,
            ],
            'session' => $session,
        ]);
        Router::setRequest($request);

        return new AppView($request, new Response());
    }
}
