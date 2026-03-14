<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use RuntimeException;

class JwksUtility
{
    /**
     * @return array<string, mixed>
     */
    public function getJwks(bool $forceRefresh = false): array
    {
        $region = Configure::readOrFail('AWS.Cognito.Region');
        $userPoolId = Configure::readOrFail('AWS.Cognito.UserPoolId');

        // JWKS URL for Cognito
        $jwksUrl = "https://cognito-idp.{$region}.amazonaws.com/{$userPoolId}/.well-known/jwks.json";

        // Force a fresh fetch if requested
        if ($forceRefresh) {
            Cache::delete('jwks-' . md5($jwksUrl));
        }

        /** @var array<string, mixed> $jwks */
        $jwks = Cache::remember('jwks-' . md5($jwksUrl), function () use ($jwksUrl) {
            $http = new Client();
            $response = $http->get($jwksUrl);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Failed to fetch JWKS keys from Cognito');
            }

            return (array)$response->getJson();
        });

        return $jwks;
    }
}
