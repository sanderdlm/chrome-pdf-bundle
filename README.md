ChromePdfBundle
===============

The ChromePdfBundle is a basic wrapper around chrome-php/chrome that enables you to quickly save rendered HTML as PDF files in your Symfony project.

Installation
------------

With [composer](https://getcomposer.org), require:

`composer require dreadnip/chrome-pdf-bundle`

Configuration
-------------

The bundle relies on a working, up-to-date Chrome/Chromium instance to work. You specify the binary in your .env file.

```yaml
# .env or .env.local
CHROME_BINARY="/usr/bin/chromium"
```

Usage
-----

The bundle registers one service:

- the `PdfGenerator` service allows you to generate pdf files from HTML strings.

### Generate a pdf document from a twig view

```php
// @var Dreadnip\ChromePdfBundle\Service
$pdfGenerator->generate(
    $twig->render(
        'MyPdf.html.twig',
        [
            'some'  => $vars,
        ]
    ),
    '/full/path/to/the/file-including-name.pdf'
);
```


### Render a pdf document as response from a controller

```php
use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TestController
{
    public function __invoke(
        Environment $twig,
        PdfGenerator $pdfGenerator
    ): Response {
        $html = $twig->render('pdf.html.twig');

        $options = [
            'printBackground' => true,
            'displayHeaderFooter' => true,
            'preferCSSPageSize' => true,
            'headerTemplate'=> "<div></div>",
            'footerTemplate' => "<div></div>",
            'scale' => 1.0,
        ];

        $path = $pdfGenerator->generate($html, 'files/test.pdf', $options);

        return new BinaryFileResponse($path);
    }
}
```

Credits
-------

This bundle is but a simple wrapper around the awesome [chrome-php/chrome](https://github.com/chrome-php/headless-chromium-php) project.