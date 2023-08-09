<?php

namespace Tests\modules\Blog;

use Humbrain\Framework\router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/[*:slug]-[i:id]', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): ResponseInterface|string
    {
        return 'index';
    }

    public function show(ServerRequestInterface $request): ResponseInterface|string
    {
        $id = $request->getAttribute('id');
        return 'show ' . $id;
    }
}
