<?php

namespace Dreadnip\ChromePdfBundle\Service;

use Exception;
use HeadlessChromium\BrowserFactory;
use Symfony\Component\Filesystem\Filesystem;

final class PdfGenerator
{
    private BrowserFactory $browserFactory;
    private Filesystem $fileSystem;
    private string $projectFolder;

    public function __construct(
        BrowserFactory $browserFactory,
        Filesystem $fileSystem,
        string $projectFolder
    ) {
        $this->browserFactory = $browserFactory;
        $this->fileSystem = $fileSystem;
        $this->projectFolder = $projectFolder;
    }

    public function generate(string $html, string $path, array $options = []): ?string
    {
        // Generate a random, temp filename and creation date
        $tempName = bin2hex(random_bytes(32)) . '.html';
        $tempPath = $this->projectFolder . 'tmp/' . $tempName;

        // Save it in a temp file (Chrome can't load HTML from a blob)
        $this->fileSystem->dumpFile($tempPath, $html);

        // Spin up a headless Chrome instance
        $browser = $this->browserFactory->createBrowser();

        try {
            // Navigate to the temp file using our headless Chrome
            $page = $browser->createPage();

            $page->navigate('file://' . $tempPath)->waitForNavigation();

            // Pdf it
            $pdf = $page->pdf($options);

            // Save the PDF to disk
            $pdf->saveToFile($path);

            // Clean up the temp file
            $this->fileSystem->remove($tempPath);

            return $path;
        } catch (Exception $exception) {
            //log the exception
        } finally {
            $browser->close();
        }

        return null;
    }
}
