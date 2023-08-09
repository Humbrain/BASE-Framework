<?php

namespace Humbrain\Framework\router;

use AltoRouter;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Router
 * @package Humbrain\Framework\router
 * Router to manage routes
 */
class Router
{
    private AltoRouter $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, string|callable $callable, string $name): void
    {
        $this->add('GET', $path, $callable, $name);
    }

    /**
     * @param string $method
     * @param string $path
     * @param callable $callback
     * @param string $name
     * @return void
     */
    public function add(string $method, string $path, string|callable $callback, string $name): void
    {
        try {
            $this->router->addRoutes([[$method, $path, $callback, $name]]);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @return void
     */
    public function post(string $path, string|callable $callable, string $name): void
    {
        $this->add('POST', $path, $callable, $name);
    }

    /**
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @return void
     */
    public function put(string $path, string|callable $callable, string $name): void
    {
        $this->add('PUT', $path, $callable, $name);
    }

    /**
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @return void
     */
    public function delete(string $path, string|callable $callable, string $name): void
    {
        $this->add('DELETE', $path, $callable, $name);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): Route|null
    {
        $result = $this->router->match($request->getUri()->getPath(), $request->getMethod());
        if ($result === false) :
            return null;
        endif;
        return new Route($result['name'], $result['target'], $result['params']);
    }

    public function generateUri(string $string, ?array $array = [], ?array $queryParams = []): ?string
    {
        try {
            $uri = $this->router->generate($string, $array);
            if (!empty($queryParams)) :
                return $uri . '?' . http_build_query($queryParams);
            else :
                return $uri;
            endif;
        } catch (Exception $e) {
            return null;
        }
    }
}
