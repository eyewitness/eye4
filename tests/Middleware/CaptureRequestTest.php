<?php

use Eyewitness\Eye\Http\Middleware\CaptureRequest;

class CaptureRequestTest extends TestCase
{
    protected $capture_request;

    protected $tag;

    public function setUp()
    {
        parent::setUp();

        $this->capture_request = new CaptureRequest;
        $this->tag = gmdate('Y_m_d_H');
    }

    public function testCacheMinutes()
    {
        $this->assertEquals(100, $this->capture_request->cacheMinutes);
    }

    public function testTerminateCallsRequestAndCycleIncrements()
    {
        $request = new \Illuminate\Http\Request;

        $m = Mockery::mock(CaptureRequest::class)->makePartial();
        $m->shouldReceive('getCycleTime')->once()->with($request)->andReturn(5);
        
        Cache::shouldReceive('add')
             ->with('eyewitness_request_count_'.$this->tag, 0, 100)
             ->once();

        Cache::shouldReceive('increment')
             ->with('eyewitness_request_count_'.$this->tag, 1)
             ->once();

        Cache::shouldReceive('add')
             ->with('eyewitness_total_execution_time_'.$this->tag, 0, 100)
             ->once();

        Cache::shouldReceive('increment')
             ->with('eyewitness_total_execution_time_'.$this->tag, 5)
             ->once();

        $m->terminate($request, null);
    }

    public function testGetCycleTime()
    {
        $m = new CaptureRequest;
        $request = new Illuminate\Http\Request;

        $this->assertGreaterThan(1, $m->getCycleTime($request));
        $this->assertInternalType("int", $m->getCycleTime($request));
    }

}
