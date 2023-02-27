<?php

declare(strict_types=1);

namespace Dreadnip\ChromePdfBundle\Service;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

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
    public function generate(
        string $html,
        string $path,
        ?array $printOptions = null,
        ?array $browserOptions = null,
        ?int $timeout = 30000
    ): string {
        $browser = $this->browserFactory->createBrowser($browserOptions ?? []);

        try {
            $page = $browser->createPage();

            $page->setHtml($html, $timeout,Page::NETWORK_IDLE);

            if ($printOptions === null) {
                $printOptions = [
                    'printBackground' => true,
                    'displayHeaderFooter' => true,
                    'preferCSSPageSize' => true,
                    'headerTemplate' => "<div></div>",
                    'footerTemplate' => "<div></div>",
                    'scale' => 1.0,
                ];
            }

            $page->pdf($printOptions)->saveToFile($path, $timeout);

            return $path;
        } finally {
            $browser->close();
        }
    }
}
