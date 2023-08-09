<?php

namespace Humbrain\Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && str_ends_with($uri, '/')) {
            $uri = substr($uri, 0, -1);
            return new Response(301, ['Location' => $uri]);
        }
        return new Response(200, [], 'Hello World');
    }
}
