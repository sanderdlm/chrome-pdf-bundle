<?php

declare(strict_types=1);

use HeadlessChromium\BrowserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('chrome_binary', '%env(resolve:CHROME_BINARY)%');

    $parameters->set('projectFolder', '%kernel.project_dir%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
        ->bind('$projectFolder', '%projectFolder%');

    $services->load('Dreadnip\ChromePdfBundle\\', __DIR__ . '/../src/*');

    $services->set(BrowserFactory::class)
        ->args([
            '%chrome_binary%',
        ]);
};
