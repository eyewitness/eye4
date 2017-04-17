<?php

use Eyewitness\Eye\Api\LegacyApi;
use Guzzle\Http\Client;

class LegacyApiTest extends TestCase
{
    protected $api;

    protected $guzzle;

    protected $response;

    public function setUp()
    {
        parent::setUp();

        $this->guzzle = Mockery::mock(Guzzle\Http\Client::class);
        $this->app->instance(Guzzle\Http\Client::class, $this->guzzle);

        $this->api = new LegacyApi;
        $this->response = new Response(200, ['Content-Type' => 'application/json'], json_encode(['ok']));
    }

    public function testDoesNotSendIfApiDisabled()
    {
        $this->app['config']['eye::api_enabled'] = false;

        $this->guzzle->shouldReceive('post')->never();

        $this->api->up();
    }

    public function testSendInstallEmail()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->sendInstallEmail([]);
    }

    public function testSendQueuePing()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->sendQueuePing('test', 0, []);
    }

    public function testSendQueueFailingPing()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->sendQueueFailingPing('test', 'other', 'default', null, null);
    }

    public function testSendSchedulerPing()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->sendSchedulerPing([]);
    }

    public function testSendWebhookPing()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->sendWebhookPing([]);
    }

    public function testUp()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->up();
    }

    public function testDown()
    {
        $this->guzzle->shouldReceive('post')->once()->andReturn($this->response);
        $this->api->down();
    }
}
