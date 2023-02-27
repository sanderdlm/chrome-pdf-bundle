ChromePdfBundle
===============

The ChromePdfBundle is a Symfony bundle that leverages the [chrome-php/chrome](https://github.com/chrome-php/chrome) project to render HTML and save the output as a PDF file.

Installation
------------

With [composer](https://getcomposer.org), require:

`composer require dreadnip/chrome-pdf-bundle`

Configuration
-------------

The bundle relies on a working, up-to-date Chrome/Chromium instance to work. You must specify the binary in your .env file.

```yaml
# .env or .env.local
CHROME_BINARY="/usr/bin/chromium"
```

Usage
-----

The bundle registers two services:

- `chrome_pdf.pdf_generator` allows you to generate pdf files from HTML strings. You can autowire the `PdfGenerator` class in your application.
- `chrome_pdf.browser_factory` is the chrome-php/chrome BrowserFactory class offered as a service within your Symfony application. Use this if you want to fine-tune the PDF generation process. You can use the PdfGenerator class as a starting point and build your custom solution from that.

### Render a pdf document from a Twig view and return it from a controller

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

        // You can use the default options
        $path = $pdfGenerator->generate($html, 'files/test.pdf');
        
        // Or control everything by passing custom options
        $printOptions = [
            'printBackground' => true,
            'displayHeaderFooter' => true,
            'preferCSSPageSize' => true,
            'headerTemplate'=> "<div></div>",
            'footerTemplate' => "<div></div>",
            'scale' => 1.0,
        ];
        
        $browserOptions = [
            'headless' => false,
            'proxyServer' => '127.0.0.1'
        ];

        $path = $pdfGenerator->generate(
            html: $html,
            path: 'files/test.pdf',
            printOptions: $options,
            browserOptions: $browserOptions,
            timeout: 5000
        );

        return new BinaryFileResponse($path);
    }
}
```
[Print options](https://github.com/chrome-php/chrome#print-as-pdf) can be used to control the rendering of the PDF. [Browser options](https://github.com/chrome-php/chrome#options) are available to control the headless Chrome instance that will be used to render the PDF. A list of all available options can be found in the chrome-php/chrome repository.

### Base template

The bundle comes with a base template that can be extended to build PDFs with. This includes helpers for page lay-out and breaking. The template comes with two blocks `styles` for CSS and `content` for the actual PDF content.

```html
{% extends '@ChromePdf/base.html.twig' %}

{% block content %}
    <section class="page page-one break-after">
        <h1>First page</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolores enim maxime quasi? Ab accusantium at commodi corporis, distinctio earum facilis harum ipsum maxime, nisi nostrum obcaecati odit officia quod voluptatem?</p>
    </section>
    <section class="page page-two">
        <h2>Second page</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolores enim maxime quasi? Ab accusantium at commodi corporis, distinctio earum facilis harum ipsum maxime, nisi nostrum obcaecati odit officia quod voluptatem?</p>
    </section>
{% endblock %}

```

Credits
-------

This bundle is nothing more than a simple wrapper around the awesome [chrome-php/chrome](https://github.com/chrome-php/headless-chromium-php) project.
