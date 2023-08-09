<?php

namespace Humbrain\Framework\modules;

use Humbrain\Framework\renderer\RendererInterface;
use Humbrain\Framework\router\Router;

class Module
{

    protected Router $router;
    protected RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {

        $this->router = $router;
        $this->renderer = $renderer;
    }
}
