<?php

declare(strict_types=1);

use HeadlessChromium\BrowserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('chrome_binary', '%env(resolve:CHROME_BINARY)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->private()
        ->autoconfigure()
        ->autowire();

    $services->set(BrowserFactory::class)
        ->args([
            '/usr/bin/chromium',
        ]);
};
