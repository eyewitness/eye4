<?php

namespace Eyewitness\Eye\Queue;

use Illuminate\Queue\Worker as OriginalWorker;
use Eyewitness\Eye\Eye;
use Config;

class Worker extends OriginalWorker
{
    /**
     * Extend the worker and place a heartbeat as it is processing.
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $connection
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    protected function getNextJob($connection, $queue)
    {
        if ($this->cache) {
            foreach (explode(',', $queue) as $tube) {
                $this->eyewitnessHeartBeat($tube);
            }
        }

        return parent::getNextJob($connection, $queue);
    }

    /**
     * Check if we have recently pinged Eyewitness for this queue. Only
     * ping if the cache has expired.
     *
     * @param  string  $tube
     * @return void
     */
    protected function eyewitnessHeartBeat($tube)
    {
        $connection = Config::get('eyewitness.temp_connection_name', 'default');

        if (! $this->cache->has('eyewitness_queue_heartbeat_'.$connection.'_'.$tube)) {
            $this->cache->add('eyewitness_queue_heartbeat_'.$connection.'_'.$tube, 1, 6);
            app(Eye::class)->api()->sendQueuePing($connection, $tube);
        }
    }
}
