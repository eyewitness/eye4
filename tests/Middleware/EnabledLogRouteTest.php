<?php

use Eyewitness\Eye\Http\Middleware\EnabledLogRoute;
use Illuminate\Http\Request;

class EnabledLogRouteTest extends TestCase
{
    protected $request;
    protected $middleware;

    public function setUp()
    {
        parent::setUp();

        $this->request = new Illuminate\Http\Request();
        $this->middleware = new EnabledLogRoute();
    }

    public function testGivesDisabledRouteWhenConfigDoesAllowIt()
    {
        $this->app['config']['eye::routes_log'] = false;

        $response = $this->middleware->filter(null, $this->request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals(json_encode(['error' => 'The log route is disabled on the server']), $response->getContent());
    }

    public function testAllowsRouteWhenConfigOk()
    {
        $this->app['config']['eye::routes_log'] = true;

        $response = $this->middleware->filter(null, $this->request);

        $this->assertNull($response);
    }
}
