<?php

namespace Eyewitness\Eye\Http\Middleware;

use Cache;

class CaptureRequest
{
    /**
     * How many minutes to cache the results.
     *
     * @var int
     */
    public $cacheMinutes = 100;

    /**
     * Handle the terminating request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $tag = gmdate('Y_m_d_H');

        $this->incrementRequestCount($tag);
        $this->incrementCycleTime($tag, $this->getCycleTime($request));
    }

    /**
     * Increment the total request count.
     *
     * @param  string  $tag
     * @return void
     */
    protected function incrementRequestCount($tag)
    {
        Cache::add('eyewitness_request_count_'.$tag, 0, $this->cacheMinutes);
        Cache::increment('eyewitness_request_count_'.$tag, 1);
    }

    /**
     * Increment the total cycle time count.
     *
     * @param  string  $tag
     * @param  integer $cycle_time
     * @return void
     */
    protected function incrementCycleTime($tag, $cycle_time)
    {
        Cache::add('eyewitness_total_execution_time_'.$tag, 0, $this->cacheMinutes);
        Cache::increment('eyewitness_total_execution_time_'.$tag, $cycle_time);
    }

    /**
     * Get the start time for this request cycle. Increments must
     * be "whole" numbers - so we also return an integer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return integer
     */
    public function getCycleTime($request)
    {
        if (! defined('LARAVEL_START')) {
            return (int) round((microtime(true) - $request->server('REQUEST_TIME_FLOAT'))*1000);
        }

        return (int) round((microtime(true) - LARAVEL_START)*1000);
    }

}
