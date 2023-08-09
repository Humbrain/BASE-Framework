<?php

namespace Humbrain\Framework;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    private array $modules = [];
    private Router $router;

    public function __construct(?array $modules = [])
    {
        $this->router = new Router();
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router);
        }
    }


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && str_ends_with($uri, '/')) {
            $uri = substr($uri, 0, -1);
            return new Response(301, ['Location' => $uri]);
        }
        $route = $this->router->match($request);
        if (is_null($route)) :
            return new Response(404, [], '<h1>Error 404</h1>');
        endif;
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $result = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($result)) :
            return new Response(200, [], $result);
        elseif ($result instanceof ResponseInterface) :
            return $result;
        endif;
        return new Response(500, [], '<h1>Error 500</h1>');
    }
}
