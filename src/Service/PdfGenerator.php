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
     * Create a PDF file at the specified path from the passed HTML string and a set of options
     *
     * @param string $html The rendered Twig template you want to save as a PDF
     * @param string $path The full path you want to save the file at, including filename
     * @param array $options The PDF options you want to use during the PDF creation
     *
     * @return string
     * @throws \Exception
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
