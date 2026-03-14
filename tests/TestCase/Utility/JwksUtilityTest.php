<?php
declare(strict_types=1);

namespace App\Test\TestCase\Utility;

use App\Utility\JwksUtility;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\TestSuite\TestCase;

class JwksUtilityTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('AWS.Cognito.Region');
        Configure::delete('AWS.Cognito.UserPoolId');
    }

    public function testGetJwksReturnsCachedKeys(): void
    {
        Configure::write('AWS.Cognito.Region', 'eu-west-1');
        Configure::write('AWS.Cognito.UserPoolId', 'pool-id');

        $jwksUrl = 'https://cognito-idp.eu-west-1.amazonaws.com/pool-id/.well-known/jwks.json';
        $expected = ['keys' => [['kid' => 'cached-key']]];
        Cache::write('jwks-' . md5($jwksUrl), $expected, 'default');

        $utility = new JwksUtility();

        $this->assertSame($expected, $utility->getJwks());
    }

    public function testGetJwksRequiresConfiguration(): void
    {
        $utility = new JwksUtility();

        $this->expectException(CakeException::class);
        $utility->getJwks();
    }
}
