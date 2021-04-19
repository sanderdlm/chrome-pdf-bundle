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

    private const TEMP_FOLDER = 'tmp/';

    public function __construct(
        BrowserFactory $browserFactory,
        RequestStack $requestStack,
        Filesystem $fileSystem
    ) {
        $this->browserFactory = $browserFactory;
        $this->requestStack = $requestStack;
        $this->fileSystem = $fileSystem;
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
        /*
         * Chrome can't load HTML straight from a string so we have to
         * save the passed HTML to a temp file and read it from there.
         */
        $tempPath = $this->generateTemporaryFilePath();

        $tempUrl = $this->getAbsoluteUrl($tempPath);

        $this->fileSystem->dumpFile($tempPath, $html);

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
            $this->fileSystem->remove($tempPath);

            return $path;
        } finally {
            $browser->close();
        }
    }

    /**
     * Generates a temporary file name in your project's public folder
     *
     * @return string
     * @throws \Exception
     */
    private function generateTemporaryFilePath(): string
    {
        return self::TEMP_FOLDER . bin2hex(random_bytes(32)) . '.html';
    }

    /**
     * Return the absolute URL for our temporary dump file
     *
     * @param string $tempPath
     * @return string
     */
    private function getAbsoluteUrl(string $tempPath): string
    {
        return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/' . $tempPath;
    }
}
