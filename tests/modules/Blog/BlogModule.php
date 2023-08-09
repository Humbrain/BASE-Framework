<?php

namespace Tests\modules\Blog;

use Humbrain\Framework\modules\Module;
use Humbrain\Framework\renderer\RendererInterface;
use Humbrain\Framework\router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule extends Module
{
    public function __construct(Router $router, RendererInterface $renderer)
    {
        parent::__construct($router, $renderer);
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/[*:slug]-[i:id]', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): ResponseInterface|string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(ServerRequestInterface $request): ResponseInterface|string
    {
        return $this->renderer->render('@blog/index');
    }
}
