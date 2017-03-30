<?php

class LogControllerTest extends TestCase
{
    protected $logMock;

    public function setUp()
    {
        parent::setUp();

        $this->logMock = Mockery::mock(Eyewitness\Eye\Witness\Log::class);
        $this->app->instance(Eyewitness\Eye\Witness\Log::class, $this->logMock);

        $this->app['router']->enableFilters();
    }

    public function testLogControllerRequiredAuthentication()
    {
        $response = $this->call('GET', $this->api.'log');

        $this->assertEquals(json_encode(['error' => 'Unauthorized']), $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testLogHonoursConfig()
    {
        $this->app['config']['eye::routes_log'] = false;

        $response = $this->call('GET', $this->api.'log'.$this->auth);
        $this->assertEquals(json_encode(['error' => 'The log route is disabled on the server']), $response->getContent());
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testLogIndexGetsLogFiles()
    {
        $this->logMock->shouldReceive('getLogFiles')
                      ->once()
                      ->andReturn(['log' => 'example']);

        $response = $this->call('GET', $this->api.'log'.$this->auth);

        $this->assertEquals(json_encode(['log_files' => ['log' => 'example']]), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLogShowHandlesFileNotFound()
    {
        $this->logMock->shouldReceive('readLogFile')->never();
        $this->logMock->shouldReceive('getLogFilenames')
                      ->once()
                      ->andReturn(['example']);

        $response = $this->call('GET', $this->api.'log/show'.$this->auth, ['filename' => 'test.log', 'count' => 3]);

        $this->assertEquals(json_encode(['error' => 'File not found']), $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testLogShow()
    {
        $this->logMock->shouldReceive('getLogFilenames')
                      ->once()
                      ->andReturn(['test.log']);

        $this->logMock->shouldReceive('readLogFile')
                      ->with('test.log', 3, 0, null)
                      ->once()
                      ->andReturn(['log' => 'example']);

        $response = $this->call('GET', $this->api.'log/show'.$this->auth, ['filename' => 'test.log', 'count' => 3]);

        $this->assertEquals(json_encode(['log' => 'example']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

}
