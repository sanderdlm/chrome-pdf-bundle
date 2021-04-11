<?php

declare(strict_types=1);

use HeadlessChromium\BrowserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('chrome_binary', '%env(resolve:CHROME_BINARY)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();
    
    $services->load('Dreadnip\ChromePdfBundle\\', __DIR__ . '/../src/*');

    $services->set(BrowserFactory::class)
        ->args([
            '%chrome_binary%',
        ]);
};
