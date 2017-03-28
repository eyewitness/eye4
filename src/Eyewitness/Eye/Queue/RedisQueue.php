<?php

namespace Eyewitness\Eye\Queue;

class RedisQueue extends Handler
{
    /**
     * Create a new Redis queue instance.
     *
     * @param string  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->queue = $connection->getRedis();
    }

    /**
     * Return the number of pending jobs for the tube.
     *
     * @param  string  $tube
     * @return int
     */
    public function pendingJobsCount($tube)
    {
        return $this->queue->llen('queues:'.$tube);
    }
}
