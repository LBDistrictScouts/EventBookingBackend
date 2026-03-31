<?php
declare(strict_types=1);

namespace LBDistrictScouts\CoreDataSeeder\ServiceProvider;

use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\ServiceProvider;
use Cake\Http\Client;
use LBDistrictScouts\CoreDataSeeder\Service\CoreDataService;

class CoreDataServiceProvider extends ServiceProvider
{
    /**
     * @var list<string>
     */
    protected array $provides = [
        CoreDataService::class,
    ];

    public function services(ContainerInterface $container): void
    {
        $container->addShared(CoreDataService::class, function () {
            return new CoreDataService(
                self::readRequiredConfig('CoreDataSeeder.url'),
                self::readRequiredConfig('CoreDataSeeder.username'),
                self::readRequiredConfig('CoreDataSeeder.password'),
                new Client(),
            );
        });
    }

    private static function readRequiredConfig(string $path): string
    {
        $value = Configure::read($path);
        if (!is_string($value) || $value === '') {
            throw new \RuntimeException(sprintf('Missing required configuration `%s`.', $path));
        }

        return $value;
    }
}
