<?php

namespace Humbrain\Framework\renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    private Environment $twig;
    private FilesystemLoader $loader;

    public function __construct(string $path)
    {
        $this->loader = new FilesystemLoader($path);
        $this->twig = new Environment($this->loader, [
            'cache' => false,
        ]);
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        try {
            $this->loader->addPath($path, $namespace);
        } catch (LoaderError $e) {
            echo $e->getMessage();
        }
    }

    public function render(string $view, array $params = []): string
    {
        try {
            return $this->twig->render($view . '.twig', $params);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return $e->getMessage();
        }
    }

    public function addGlobal(string $key, string $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
