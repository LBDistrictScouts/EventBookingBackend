<?php
declare(strict_types=1);

namespace App\Controller;

use App\Utility\JwksUtility;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Client;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Routing\Router;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;
use RuntimeException;

class AuthController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // 🔹 Bypass authentication for these actions
        $this->Authentication->allowUnauthenticated(['login', 'callback', 'logout']);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        if ($this->request->getSession()->check('Auth.User')) {
            return $this->redirect('/events/current'); // ✅ Prevents looping
        }

        $cognitoDomain = Configure::readOrFail('AWS.Cognito.Domain');
        $authUrl = "https://{$cognitoDomain}/oauth2/authorize?" . http_build_query([
                'response_type' => 'code',
                'client_id' => Configure::readOrFail('AWS.Cognito.ClientId'),
                'redirect_uri' => Router::url('/auth/callback', true),
                'scope' => 'openid email profile',
            ]);

        return $this->redirect($authUrl);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function callback(): ?Response
    {
        $code = $this->getRequest()->getQuery('code');

        if (!$code) {
            throw new UnauthorizedException('Authorization code missing.');
        }

        $tokens = $this->fetchTokens($code);

        // ✅ Extract JWT header to find the "kid"
        $jwtHeader = json_decode(base64_decode(explode('.', $tokens['id_token'])[0]), true);

        // ✅ Ensure the JWT contains "kid"
        $kid = $jwtHeader['kid'] ?? null;
        if (!$kid) {
            throw new UnauthorizedException("Missing 'kid' in JWT header.");
        }

        // ✅ Fetch the correct public key using the "kid"
        $publicKey = $this->getPublicKey($kid);

        // ✅ Decode the JWT using Firebase JWT
        $decodedJwt = JWT::decode($tokens['id_token'], new Key($publicKey, 'RS256'));

        // ✅ Store user identity in session
        $this->request->getSession()->write('Auth.User', [
            'email' => $decodedJwt->email,
            'subject' => $decodedJwt->sub,
            'first_name' => $decodedJwt->given_name ?? '',
            'last_name' => $decodedJwt->family_name ?? '',
            'token' => $tokens['id_token'],
        ]);

        $expiresInSeconds = $decodedJwt->exp - time();
        if ($expiresInSeconds > 0) {
            // Set session timeout
            $this->request->getSession()->write('Auth.expires_at', $decodedJwt->exp);
        }

        // 🚀 Redirect to the dashboard instead of looping back to login
        return $this->redirect('/events/current');
    }

    /**
     * @param string $code
     * @return array
     */
    private function fetchTokens(string $code): array
    {
        $http = new Client();
        $cognitoDomain = Configure::readOrFail('AWS.Cognito.Domain');
        $clientId = Configure::readOrFail('AWS.Cognito.ClientId');
        $clientSecret = Configure::read('AWS.Cognito.ClientSecret');

        $redirectUri = Router::url('/auth/callback', true);

        $response = $http->post("https://{$cognitoDomain}/oauth2/token", [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ], ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded']]);

        if ($response->getStatusCode() !== 200) {
            error_log('Failed to retrieve token: ' . $response->getBody());

            throw new UnauthorizedException('Failed to retrieve token: ' . $response->getBody());
        }

        return $response->getJson();
    }

    /**
     * @param string $kid The Key ID from the JWT header.
     * @return string|null The matching public key in PEM format.
     */
    private function getPublicKey(string $kid): ?string
    {
        $jwksUtil = new JwksUtility();
        $jwks = $jwksUtil->getJwks();

        foreach ($jwks['keys'] as $key) {
            if ($key['kid'] === $kid) {
                return $this->convertJwkToPem($key);
            }
        }

        // 🔹 If key not found, force refresh JWKS and retry
        $jwks = $jwksUtil->getJwks(true); // 🔄 Force JWKS refresh

        foreach ($jwks['keys'] as $key) {
            if ($key['kid'] === $kid) {
                return $this->convertJwkToPem($key);
            }
        }

        throw new RuntimeException("No matching public key found in JWKS for kid: {$kid}");
    }

    /**
     * Converts a JWK (JSON Web Key) to a PEM public key.
     *
     * @param array $jwk The JSON Web Key from Cognito.
     * @return string The PEM-formatted public key.
     */
    private function convertJwkToPem(array $jwk): string
    {
        if (!isset($jwk['n']) || !isset($jwk['e'])) {
            throw new RuntimeException('Invalid JWK: missing modulus (n) or exponent (e)');
        }

        // Decode the base64url-encoded modulus (n) and exponent (e)
        $modulus = new BigInteger(base64_decode(str_replace(['-', '_'], ['+', '/'], $jwk['n'])), 256);
        $exponent = new BigInteger(base64_decode(str_replace(['-', '_'], ['+', '/'], $jwk['e'])), 256);

        // Load RSA key from modulus and exponent
        $rsa = RSA::loadPublicKey([
            'n' => $modulus,
            'e' => $exponent,
        ]);

        // Return the key in PEM format
        return $rsa->toString('PKCS8');
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->request->getSession()->destroy();

        return $this->redirect('https://' . Configure::read('AWS.Cognito.Domain') . '/logout?' . http_build_query([
                'client_id' => Configure::read('AWS.Cognito.ClientId'),
                'logout_uri' => Router::url('/', true),
            ]));
    }
}
