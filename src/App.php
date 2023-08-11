<?php

namespace Humbrain\Framework;

use DI\ContainerBuilder;
use Exception;
use Humbrain\Framework\modules\Module;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class App implements Handler
{
    /** @var Module[] */
    private array $modules;
    private string $definition;

    /** @var string[] */
    private array $middlewares = [];

    private int $index = 0;

    private ContainerInterface $container;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) :
            try {
                $this->getContainer()->get($module);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                continue;
            }
        endforeach;
        return $this->handle($request);
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        if (isset($this->container)) :
            return $this->container;
        endif;
        $builder = new ContainerBuilder();
        $builder->addDefinitions($this->definition);
        foreach ($this->modules as $module) {
            if (!is_null($module::DEFINITIONS)) :
                $builder->addDefinitions($module::DEFINITIONS);
            endif;
        }
        try {
            $this->container = $builder->build();
        } catch (Exception $e) {
            return $this->container;
        }

        return $this->container;
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleWare();
        if (is_null($middleware)) :
            throw new Exception('Aucun middleware n\'a intercepté cette requête');
        elseif (is_callable($middleware)) :
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        elseif ($middleware instanceof MiddlewareInterface) :
            return $middleware->process($request, $this);
        endif;
        throw new Exception('Le middleware n\'est pas valide');
    }

    private function getMiddleWare(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) :
            $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        endif;
        return null;
    }
}
