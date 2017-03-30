<?php

class FailedQueueControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app['router']->enableFilters();
    }

    public function testControllerRequiredAuthentication()
    {
        $response = $this->call('GET', $this->api.'failed_queue');

        $this->assertEquals(json_encode(['error' => 'Unauthorized']), $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testFailedQueueHonoursConfig()
    {
        $this->app['config']['eye::routes_queue'] = false;

        $response = $this->call('GET', $this->api.'failed_queue'.$this->auth);
        $this->assertEquals(json_encode(['error' => 'The queue route is disabled on the server']), $response->getContent());
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testIndexGetsFailedJobs()
    {
        $queueMock = Mockery::mock(Eyewitness\Eye\Witness\Queue::class);
        $this->app->instance(Eyewitness\Eye\Witness\Queue::class, $queueMock);
        $queueMock->shouldReceive('getFailedJobs')->once()->andReturn(['example' => 'job']);

        $response = $this->call('GET', $this->api.'failed_queue'.$this->auth);

        $this->assertEquals(json_encode(['data' => ['example' => 'job']]), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteFailedJobIsSuccessful()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('forget')
          ->with(1)
          ->once()
          ->andReturn(true);

        $response = $this->call('GET', $this->api.'failed_queue/delete/1'.$this->auth);

        $this->assertEquals(json_encode(['msg' => 'Success']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteFailedJobThatDoesNotExistGeneratesError()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('forget')
          ->with(1)
          ->once()
          ->andReturn(false);

        $response = $this->call('GET', $this->api.'failed_queue/delete/1'.$this->auth);

        $this->assertEquals(json_encode(['error' => 'Could not find that log id to delete']), $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteFailedJobCatchesException()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('forget')
          ->with(1)
          ->once()
          ->andThrow(new Exception('my error'));

        $response = $this->call('GET', $this->api.'failed_queue/delete/1'.$this->auth);

        $this->assertEquals(json_encode(['error' => 'my error']), $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testDeleteAllFailedJobIsSuccessful()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('flush')
          ->once()
          ->andReturn(true);

        $response = $this->call('GET', $this->api.'failed_queue/delete/all'.$this->auth);

        $this->assertEquals(json_encode(['msg' => 'Success']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteAllFailedJobThatDoesNotExistIsOk()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('flush')
          ->once()
          ->andReturn(false);

        $response = $this->call('GET', $this->api.'failed_queue/delete/all'.$this->auth);

        $this->assertEquals(json_encode(['msg' => 'Success']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteAllFailedJobCatchesException()
    {
        $m = Mockery::mock(Illuminate\Queue\Failed\NullFailedJobProvider::class);

        $this->app->singleton('queue.failer', function ($app) use ($m) {
            return $m;
        });

        $m->shouldReceive('flush')
          ->once()
          ->andThrow(new Exception('my error'));

        $response = $this->call('GET', $this->api.'failed_queue/delete/all'.$this->auth);

        $this->assertEquals(json_encode(['error' => 'my error']), $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testRetryJobIsSuccessful()
    {
        Artisan::shouldReceive('call')
               ->with('queue:retry', ['id' => [1]])
               ->once()
               ->andReturn(true);

        $response = $this->call('GET', $this->api.'failed_queue/retry/1'.$this->auth);

        $this->assertEquals(json_encode(['msg' => 'Success']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRetryJobHandlesException()
    {
        Artisan::shouldReceive('call')
               ->with('queue:retry', ['id' => [1]])
               ->once()
               ->andThrow(new Exception('my error'));

        $response = $this->call('GET', $this->api.'failed_queue/retry/1'.$this->auth);

        $this->assertEquals(json_encode(['error' => 'my error']), $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testRetryAllJobIsSuccessful()
    {
        Artisan::shouldReceive('call')
               ->with('queue:retry', ['id' => ['all']])
               ->once()
               ->andReturn(true);

        $response = $this->call('GET', $this->api.'failed_queue/retry/all'.$this->auth);

        $this->assertEquals(json_encode(['msg' => 'Success']), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
