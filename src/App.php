<?php

namespace Humbrain\Framework;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    private array $modules;
    private Router $router;
    private ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     * @param array $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            try {
                $this->modules[] = $container->get($module);
            } catch (ContainerExceptionInterface $e) {
                return;
            }
        }
    }


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && str_ends_with($uri, '/')) {
            $uri = substr($uri, 0, -1);
            return new Response(301, ['Location' => $uri]);
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) :
            return new Response(404, [], '<h1>Error 404</h1>');
        endif;
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        if (is_string($route->getCallback())) :
            $callback = $this->container->get($route->getCallback());
        else :
            $callback = $route->getCallback();
        endif;
        $result = call_user_func_array($callback, [$request]);
        if (is_string($result)) :
            return new Response(200, [], $result);
        elseif ($result instanceof ResponseInterface) :
            return $result;
        endif;
        return new Response(500, [], '<h1>Error 500</h1>');
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
