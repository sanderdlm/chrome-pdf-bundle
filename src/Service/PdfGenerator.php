<?php

declare(strict_types=1);

namespace Dreadnip\ChromePdfBundle\Service;

use HeadlessChromium\BrowserFactory;

final class PdfGenerator
{
    private BrowserFactory $browserFactory;

    public function __construct(
        BrowserFactory $browserFactory
    ) {
        $this->browserFactory = $browserFactory;
    }

    /**
     * @param array<string, string> $printOptions
     * @param array<string, string> $browserOptions
     */
    public function generate(string $html, string $path, array $printOptions = [], array $browserOptions = []): string
    {
        $browser = $this->browserFactory->createBrowser($browserOptions);

        try {
            $page = $browser->createPage();

            $page->setHtml($html);

            $page->pdf($printOptions)->saveToFile($path, 300000);

            return $path;
        } finally {
            $browser->close();
        }
    }
}
