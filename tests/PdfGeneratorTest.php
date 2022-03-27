<?php

namespace Dreadnip\ChromePdfBundle\Test;

use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use HeadlessChromium\BrowserFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers Dreadnip\ChromePdfBundle\Service\PdfGenerator
 */
class PdfGeneratorTest extends TestCase
{
    public function testPdfGeneration(): void
    {
        $browserFactory = new BrowserFactory('/usr/bin/chromium');

        $generator = new PdfGenerator($browserFactory);

        $html = file_get_contents('tests/test_source.html');

        $path = __DIR__ . '/test.pdf';

        $generator->generate($html, $path);

        $this->assertFileExists($path);

        unlink($path);
    }
}
