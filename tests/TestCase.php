<?php

declare(strict_types=1);

namespace Tests\Tempest\Console;

use Tempest\AppConfig;
use Tempest\Application;
use Tempest\Console\ConsoleApplication;
use Tempest\Console\Input\ConsoleArgumentBag;
use Tempest\Console\Scheduler\NullShellExecutor;
use Tempest\Console\ShellExecutor;
use Tempest\Console\Testing\ConsoleTester;
use Tempest\Container\Container;
use Tempest\Discovery\DiscoveryLocation;
use Tempest\Kernel;

/**
 * @internal
 * @small
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;
    protected ConsoleTester $console;

    protected function setUp(): void
    {
        parent::setUp();

        $appConfig = new AppConfig(
            root: getcwd(),
            enableExceptionHandling: true,
            discoveryLocations: [
                new DiscoveryLocation('Tests\\Tempest\\Console\\', __DIR__),
            ],
        );

        $kernel = new Kernel($appConfig);

        $this->container = $kernel->init();

        $application = new ConsoleApplication(
            container: $this->container,
            argumentBag: new ConsoleArgumentBag($_SERVER['argv']),
        );

        $this->container->singleton(Application::class, fn () => $application);
        $this->container->singleton(ShellExecutor::class, fn () => new NullShellExecutor());
        $this->console = new ConsoleTester($this->container);
    }
}
