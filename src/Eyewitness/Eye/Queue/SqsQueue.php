<?php

namespace Eyewitness\Eye\Queue;

use Aws\Sqs\Exception\SqsException;

class SqsQueue extends Handler
{
    /**
     * Create a new Sqs queue instance.
     *
     * @param string  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->queue = $connection;
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
            $result = $this->queue->getSqs()->getQueueAttributes([
                            'QueueUrl' => $this->queue->getQueue($tube),
                            'AttributeNames' => ['ApproximateNumberOfMessages']
                            ]);

            $count = $result['Attributes']['ApproximateNumberOfMessages'];
        } catch (SqsException $e) {
            $count = 0;
        }

        return $count;
    }
}
