<?php

declare(strict_types=1);

namespace Dreadnip\ChromePdfBundle\Service;

use HeadlessChromium\BrowserFactory;
use Symfony\Component\Filesystem\Filesystem;

final class PdfGenerator
{
    private BrowserFactory $browserFactory;
    private Filesystem $fileSystem;

    public function __construct(
        BrowserFactory $browserFactory,
        Filesystem $fileSystem
    ) {
        $this->browserFactory = $browserFactory;
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
     */
    public function generate(string $html, string $path, array $options = []): string
    {
        /*
         * Chrome can't load HTML straight from a string so we have to
         * save the passed HTML to a temp file and read it from there.
         */
        $tempPath = $this->generateTemporaryFilePath();
        $this->fileSystem->dumpFile($tempPath, $html);

        $browser = $this->browserFactory->createBrowser();

        try {
            $page = $browser->createPage();
            $page->navigate('file://' . $tempPath)->waitForNavigation();

            $pdf = $page->pdf($options);

            $pdf->saveToFile($path);

            // Clean up the temp file
            $this->fileSystem->remove($tempPath);

            return $path;
        } finally {
            $browser->close();
        }
    }

    /**
     * Generates a temporary file name in the system temp dir
     *
     * @return string
     */
    private function generateTemporaryFilePath(): string
    {
        return sys_get_temp_dir() . '/' . bin2hex(random_bytes(32)) . '.html';
    }
}
