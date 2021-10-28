<?php

namespace Dreadnip\ChromePdfBundle\Test;

use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use Dreadnip\ChromePdfBundle\Test\WebServer\WebServerManager;
use HeadlessChromium\BrowserFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers Dreadnip\ChromePdfBundle\Service\PdfGenerator
 */
class PdfGeneratorTest extends TestCase
{
    public function testPdfGeneration(): void
    {
        $webserver = new WebServerManager(__DIR__);
        $webserver->start();

        $request = new Request([], [], [], [], [], [
            'SERVER_ADDR' => 'localhost',
            'SERVER_PORT' => 45066,
        ],[]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $browserFactory = new BrowserFactory('/usr/bin/chromium');

        // create a generator
        $generator = new PdfGenerator($browserFactory, $requestStack, __DIR__ . '/');

        $html = '<html><head></head><body>This is a test!</body></html>';

        $path = __DIR__ . '/test.pdf';

        $generator->generate($html, $path);

        $this->assertFileExists($path);

        $webserver->quit();
        unlink($path);
    }
}
