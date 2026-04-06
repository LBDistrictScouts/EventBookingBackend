<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class AuthControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        Configure::write('AWS.Cognito.Domain', 'example.auth.eu-west-1.amazoncognito.com');
        Configure::write('AWS.Cognito.ClientId', 'test-client-id');
        Router::fullBaseUrl('http://localhost');
    }

    public function testLoginStoresSafeRedirectTargetInSession(): void
    {
        $this->get('/auth/login?redirect=%2Fsections%2Fview%2F95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertRedirectContains('https://example.auth.eu-west-1.amazoncognito.com/oauth2/authorize');
        $this->assertSession(
            '/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'Auth.redirect_target',
        );
    }

    public function testLoginIgnoresUnsafeRedirectTarget(): void
    {
        $this->get('/auth/login?redirect=https%3A%2F%2Fevil.example.com');

        $this->assertRedirectContains('https://example.auth.eu-west-1.amazoncognito.com/oauth2/authorize');
        $this->assertSession(null, 'Auth.redirect_target');
    }

    public function testLoggedInUserIsRedirectedToRequestedTarget(): void
    {
        $this->session([
            'Auth.User' => [
                'email' => 'leader@example.com',
            ],
        ]);

        $this->get('/auth/login?redirect=%2Fsections%2Fview%2F95116a77-0675-4e1a-9d0c-74e3d40d92c1');

        $this->assertRedirect('/sections/view/95116a77-0675-4e1a-9d0c-74e3d40d92c1');
    }
}
