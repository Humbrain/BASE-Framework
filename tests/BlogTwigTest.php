<?php


namespace Tests;

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\App;
use Humbrain\Framework\renderer\PHPRenderer;
use Humbrain\Framework\renderer\TwigRenderer;
use Humbrain\Framework\router\Router;
use PHPUnit\Framework\TestCase;
use Tests\modules\Blog\BlogModule;

class BlogTwigTest extends TestCase
{
    private Router $router;
    private App $blog;

    public function setUp(): void
    {
        $this->app = new App([BlogModule::class], [
            "renderer" => new TwigRenderer(__DIR__ . '/views')
        ]);
    }

    public function testBlogIndex()
    {

        $request = new ServerRequest('GET', '/blog');
        $route = $this->app->run($request);
        $this->assertEquals('hello world', (string)$route->getBody());
    }

    public function testBlogShow()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $route = $this->app->run($request);
        $this->assertEquals('hello world', (string)$route->getBody());
    }
}
