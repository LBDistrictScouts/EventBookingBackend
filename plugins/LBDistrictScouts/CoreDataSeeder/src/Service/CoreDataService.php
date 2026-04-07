<?php
declare(strict_types=1);

namespace LBDistrictScouts\CoreDataSeeder\Service;

use Cake\Http\Client;
use RuntimeException;

class CoreDataService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $username,
        private readonly string $password,
        private readonly Client $httpClient = new Client(),
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getSections(): array
    {
        return $this->getJson('/sections.json');
    }

    /**
     * @param string $path
     * @return array<string, mixed>
     */
    public function getJson(string $path): array
    {
        $response = $this->httpClient->get($this->buildUrl($path), [], [
            'auth' => [
                'username' => $this->username,
                'password' => $this->password,
            ],
        ]);

        if (!$response->isSuccess()) {
            throw new RuntimeException(sprintf(
                'Core Data request failed for `%s` with status %d.',
                $path,
                $response->getStatusCode(),
            ));
        }

        $payload = $response->getJson();
        if (!is_array($payload)) {
            throw new RuntimeException(sprintf(
                'Core Data response for `%s` did not contain a JSON object or array.',
                $path,
            ));
        }

        return $payload;
    }

    private function buildUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
