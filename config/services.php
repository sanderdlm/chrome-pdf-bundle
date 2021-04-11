<?php

declare(strict_types=1);

use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use HeadlessChromium\BrowserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('chrome_binary', '%env(resolve:CHROME_BINARY)%');

    $services = $containerConfigurator->services();

    $services->load('Dreadnip\ChromePdfBundle\\', __DIR__ . '/../src/*');

    $services->set(BrowserFactory::class)
        ->args([
            '%chrome_binary%',
        ]);

    $services->set(PdfGenerator::class)
        ->args([
            BrowserFactory::class,
            Filesystem::class,
        ]);
};
