<?php

namespace Eyewitness\Eye\Witness;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\Collection;
use Exception;
use Config;

class Queue
{
    /**
     * Get the queue connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    public function getConnectionConfig($connection)
    {
        return app('config')["queue.connections.$connection"];
    }

    /**
     * Get the queue stats for all tubes.
     *
     * @return mixed
     */
    public function allTubeStats()
    {
        $stats = [];

        try {
            foreach (Config::get('eye::queue_tube_list') as $connection => $tubes) {
                foreach ($tubes as $tube) {
                    $stats[] = $this->tubeStats($connection, $tube);
                }
            }
        } catch (Exception $e) {
            //
        }

        return $stats;
    }

    /**
     * Get the queue stats for a specific tube.
     *
     * @param  string  $connection
     * @param  string  $tube
     * @return mixed
     */
    public function tubeStats($connection, $tube)
    {
        $stats['connection'] = $connection;
        $stats['tube'] = $tube;
        $stats['pending_count'] = $this->getPendingJobsCount($connection, $tube);
        $stats['failed_count'] = $this->getFailedJobsCount($connection, $tube);

        return $stats;
    }

    /**
     * Count the number of failed jobs.
     *
     * @param  string  $connection
     * @param  string  $tube
     * @return int
     */
    protected function getFailedJobsCount($connection, $tube)
    {
        return $this->getFailedJobs()->filter(function($job) use ($tube) {
                                                  return $job->queue == $tube;
                                   })->filter(function($job) use ($connection) {
                                                  return $job->connection == $connection;
                                   })->count();
    }

    /**
     * Get a list of failed jobs.
     *
     * @return mixed
     */
    public function getFailedJobs()
    {
        try {
            $list = new Collection(app('queue.failer')->all());
            $list->map(function ($job) {
                $payload = json_decode($job->payload);
                $job->job = isset($payload->job) ? $payload->job : 'Unknown';
                return $job;
            });
            return $list;
        } catch (Exception $e) {
            return new Collection([]);
        }
    }

    /**
     * Get the number of pending jobs for this queue tube.
     *
     * @param  string  $connection
     * @param  string  $tube
     * @return void
     */
    protected function getPendingJobsCount($connection, $tube)
    {
        $config = $this->getConnectionConfig($connection);
        $driver_class = "\\Eyewitness\\Eye\\Queue\\".ucfirst(strtolower($config['driver'])).'Queue';

        if (class_exists($driver_class)) {
            $qm = app(QueueManager::class)->connection($connection);
            return (new $driver_class($qm, $config))->pendingJobsCount($tube);
        }

        return 0;
    }
}

