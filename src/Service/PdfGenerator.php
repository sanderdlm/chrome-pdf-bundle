<?php

declare(strict_types=1);

namespace Dreadnip\ChromePdfBundle\Service;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;

final class PdfGenerator
{
    private BrowserFactory $browserFactory;
    private RequestStack $requestStack;
    private Filesystem $fileSystem;
    private string $projectDir;

    public function __construct(
        BrowserFactory $browserFactory,
        RequestStack $requestStack,
        string $projectDir
    ) {
        $this->browserFactory = $browserFactory;
        $this->requestStack = $requestStack;
        $this->fileSystem = new Filesystem();
        $this->projectDir = $projectDir;
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
        $relativeTempFilePath = 'tmp/' . bin2hex(random_bytes(32)) . '.html';

        $absoluteTempFilePath = $this->projectDir . $relativeTempFilePath;

        $this->fileSystem->dumpFile($absoluteTempFilePath, $html);

        $tempUrl = $this->getUrl($relativeTempFilePath);

        $browser = $this->browserFactory->createBrowser($browserOptions);

        try {
            $page = $browser->createPage();

            /*
             * We check for "network idle" instead of "load" by default to avoid
             * missing resources like images and webfonts that can take a split second longer to load
             */
            $page->navigate($tempUrl)->waitForNavigation(Page::NETWORK_IDLE);

            $page->pdf($printOptions)->saveToFile($path);

            // Clean up the temp file
            $this->fileSystem->remove($this->projectDir . 'tmp');

            return $path;
        } finally {
            $browser->close();
        }
    }

    /**
     * Return the absolute URL for our temporary dump file
     *
     * @param string $path
     * @return string
     */
    private function getUrl(string $path): string
    {
        return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/' . $path;
    }
}
