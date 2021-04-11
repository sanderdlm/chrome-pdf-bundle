<?php

declare(strict_types=1);

use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
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

    $services->set('browser_factory', BrowserFactory::class)
        ->args([
            '%chrome_binary%',
        ]);

    $services->set(PdfGenerator::class)
        ->args([
            '@browser_factory',
            '@filesystem',
        ]);
};
