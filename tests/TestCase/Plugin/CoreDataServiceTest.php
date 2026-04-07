<?php
declare(strict_types=1);

namespace App\Test\TestCase\Plugin;

use Cake\Http\Client;
use Cake\Http\Client\Response;
use Cake\TestSuite\TestCase;
use LBDistrictScouts\CoreDataSeeder\Service\CoreDataService;
use RuntimeException;

class CoreDataServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Client::clearMockResponses();

        parent::tearDown();
    }

    public function testGetSectionsCallsConfiguredEndpointWithBasicAuth(): void
    {
        Client::addMockResponse(
            'GET',
            'https://core-data.test/sections.json',
            new Response(
                ['HTTP/1.1 200 OK', 'Content-Type: application/json'],
                '{"sections":[{"id":1,"name":"Beavers"}]}',
            ),
            [
                'match' => function ($request): bool {
                    return $request->getHeaderLine('Authorization') === 'Basic ' . base64_encode('core-user:core-pass');
                },
            ],
        );

        $service = new CoreDataService(
            'https://core-data.test',
            'core-user',
            'core-pass',
        );

        $response = $service->getSections();

        $this->assertSame(
            ['sections' => [['id' => 1, 'name' => 'Beavers']]],
            $response,
        );
    }

    public function testGetJsonThrowsOnNonSuccessStatus(): void
    {
        Client::addMockResponse(
            'GET',
            'https://core-data.test/sections.json',
            new Response(
                ['HTTP/1.1 401 Unauthorized', 'Content-Type: application/json'],
                '{"error":"nope"}',
            ),
        );

        $service = new CoreDataService(
            'https://core-data.test',
            'core-user',
            'core-pass',
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Core Data request failed');

        $service->getSections();
    }
}
