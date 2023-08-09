<?php


namespace Tests;

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\App;
use Humbrain\Framework\router\Router;
use PHPUnit\Framework\TestCase;
use Tests\modules\Blog\BlogModule;

class BlogTest extends TestCase
{
    private Router $router;
    private App $blog;

    public function setUp(): void
    {
        $this->app = new App([
            BlogModule::class
        ]);
    }

    public function testBlogIndex()
    {

        $request = new ServerRequest('GET', '/blog');
        $route = $this->app->run($request);
        $this->assertEquals('index', (string)$route->getBody());
    }

    public function testBlogShow()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $route = $this->app->run($request);
        $this->assertEquals('show 8', (string)$route->getBody());
    }
}
