<?php

namespace Dreadnip\ChromePdfBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChromePdfBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}