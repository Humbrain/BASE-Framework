<?php


use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    public function setUp(): void
    {
        $this->router = new Router();
        parent::setUp();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfUrlDoesNotExists()
    {
        $request = new ServerRequest('GET', '/error');
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'posts.index');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameter()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'hello';
        }, 'posts.show');
        $route = $this->router->match($request);
        $this->assertEquals('posts.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
    }

    public function testGenerateUrl()
    {
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'posts.index');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'hello';
        }, 'posts.show');
        $url = $this->router->generateUri('posts.show', ['slug' => 'mon-article', 'id' => 18]);
        $url2 = $this->router->generateUri('posts.index');
        $this->assertEquals('/blog/mon-article-18', $url);
        $this->assertEquals('/blog', $url2);
    }

    public function testGenerateUrlFake()
    {
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'posts.index');
        $url = $this->router->generateUri('posts.show');
        $this->assertEquals(null, $url);
    }
}
