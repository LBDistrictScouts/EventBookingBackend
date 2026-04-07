<?php
declare(strict_types=1);

namespace App\Test\TestCase\Plugin;

use App\Application;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\TestSuite\TestCase;
use LBDistrictScouts\CoreDataSeeder\Service\CoreDataService;
use ReflectionProperty;

class CoreDataSeederPluginTest extends TestCase
{
    protected function tearDown(): void
    {
        Configure::delete('CoreDataSeeder');

        parent::tearDown();
    }

    public function testContainerCanResolveCoreDataServiceFromConfigureValues(): void
    {
        Configure::write('CoreDataSeeder', [
            'url' => 'https://core-data.test',
            'username' => 'core-user',
            'password' => 'core-pass',
        ]);

        $app = new Application(dirname(dirname(dirname(__DIR__))) . '/config');
        $app->bootstrap();

        /** @var \LBDistrictScouts\CoreDataSeeder\Service\CoreDataService $service */
        $service = $app->getContainer()->get(CoreDataService::class);

        $this->assertInstanceOf(CoreDataService::class, $service);
        $this->assertSame('https://core-data.test', $this->readProperty($service, 'baseUrl'));
        $this->assertSame('core-user', $this->readProperty($service, 'username'));
        $this->assertSame('core-pass', $this->readProperty($service, 'password'));
        $this->assertInstanceOf(Client::class, $this->readProperty($service, 'httpClient'));
    }

    private function readProperty(object $object, string $property): mixed
    {
        $reflection = new ReflectionProperty($object, $property);

        return $reflection->getValue($object);
    }
}
