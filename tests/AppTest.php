<?php

namespace Humbrain\test;

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testRedirectTrailingSlash()
    {
        $request = new ServerRequest('GET', '/test/');
        $app = new App();
        $response = $app->run($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/test'], $response->getHeader('Location'));

    }

    public function test404() {
        $request = new ServerRequest('GET', '/aze');
        $app = new App();
        $response = $app->run($request);
        $this->assertEquals(404, $response->getStatusCode());
    }
}