<?php

declare(strict_types=1);

use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use HeadlessChromium\BrowserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    $parameters = $containerConfigurator->parameters();

    $parameters->set('chrome_binary', '%env(resolve:CHROME_BINARY)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->set('chrome_pdf.browser_factory', BrowserFactory::class)
        ->args([
            '%chrome_binary%',
        ]);

    $services->alias(BrowserFactory::class, 'chrome_pdf.browser_factory');

    $services->set('chrome_pdf.pdf_generator', PdfGenerator::class);

    $services->alias(PdfGenerator::class, 'chrome_pdf.pdf_generator');
};
