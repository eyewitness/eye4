<?php

namespace Eyewitness\Eye\Queue;

abstract class Handler
{
    /**
     * The queue connection.
     *
     * @var string
     */
    protected $queue;

    /**
     * Return the pending job count for the tube.
     * 
     * Although similar functionality already exists in Laravel 
     * >=5.3 - we need to provide backwards compatibility to all
     * Laravel versions. 
     *
     * Also we only want to know the count of pending jobs ready
     * to be processed - not delayed or reservered jobs, which
     * is slightly different from the core framework function.
     *
     * @param  string  $tube
     * @return int
     */
    public function pendingJobsCount($tube)
    {
        return 0;
    }
}
