<?php

namespace Eyewitness\Eye\Queue;

use Pheanstalk\Exception\ServerException;

class BeanstalkdQueue extends Handler
{
    /**
     * Create a new Beanstalkd queue instance.
     *
     * @param string  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->queue = $connection->getPheanstalk();
    }

    /**
     * Return the number of pending jobs for the tube.
     *
     * @param  string  $tube
     * @return int
     */
    public function pendingJobsCount($tube)
    {
        try {
            $count = $this->queue->statsTube($tube)->{'current-jobs-ready'};
        } catch (ServerException $e) {
            $count = 0;
        }

        return $count;
    }
}
