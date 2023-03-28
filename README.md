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

- `chrome_pdf.pdf_generator` allows you to generate pdf files from HTML strings. You can autowire the `PdfGenerator` class in your application to get started quickly.
- `chrome_pdf.browser_factory` is the chrome-php/chrome BrowserFactory class offered as a service within your Symfony application. Use this if you want to fine-tune the PDF generation process. You can use the PdfGenerator class as a starting point and build your custom solution from that.

### Basic example: render a pdf document in a controller

```php
use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TestController extends AbstractController
{
    public function __invoke(PdfGenerator $pdfGenerator): Response
    {
        $html = $this->render('pdf.html.twig');

        $path = $pdfGenerator->generate($html, 'files/test.pdf');
   
        return new BinaryFileResponse($path);
    }
}
```

### Advanced example: render a pdf document in a controller with custom options

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

        // Control everything by passing custom options
        $printOptions = [
            'printBackground' => true,
            'displayHeaderFooter' => true,
            'preferCSSPageSize' => true,
            'headerTemplate'=> "<div></div>",
            'footerTemplate' => "<div></div>",
            'scale' => 1.0,
        ];
        
        // Setting headless to false helps you debug issues
        $browserOptions = [
            'headless' => false,
        ];

        $path = $pdfGenerator->generate($html, 'files/test.pdf', $options, $browserOptions);

        return new BinaryFileResponse($path);
    }
}
```
[Print options](https://github.com/chrome-php/chrome#print-as-pdf) can be used to control the rendering of the PDF.

[Browser options](https://github.com/chrome-php/chrome#options) are available to control the headless Chrome instance that will be used to render the PDF. 

A list of all available options can be found in the chrome-php/chrome repository.

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
