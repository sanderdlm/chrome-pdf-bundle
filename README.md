ChromePdfBundle
===============

The ChromePdfBundle is a basic wrapper that leverages the `chrome-php` project headless Chrome to quickly save rendered HTML as PDF files in your Symfony project.

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
- `chrome_pdf.browser_factory` is simply the chrome-php/chrome BrowserFactory class offered as a service within your Symfony application.

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

This bundle is but a simple wrapper around the awesome [chrome-php/chrome](https://github.com/chrome-php/headless-chromium-php) project.