<?php

namespace Eyewitness\Eye\Witness;

use Config;

class Scheduler
{
    /**
     * Is this specific command being tracked.
     *
     * @var boolean
     */
    protected $isTracked = false;

    /**
     * The command name.
     *
     * @var string
     */
    protected $command;

    /**
     * The command start time.
     *
     * @var integer
     */
    protected $start;

    /**
     * Get a list of all scheduled events and their cron frequency.
     *
     * @return array
     */
    public function getScheduledEvents()
    {
        return Config::get('eye::scheduled_monitor_list');
    }

    /**
     * Inspect an Artisan command and decide should be tracked as a scheduled event.
     *
     * @param  string  $server
     * @return void
     */
    public function inspectCommand($command)
    {
        if (isset($command[1]) && array_key_exists($command[1], $this->getScheduledEvents())) {
            $this->command = $command[1];
            $this->start = microtime(true);
            $this->isTracked = true;
        }
    }

    /**
     * Handle the completed command tracking if required.
     *
     * @return array
     */
    public function trackCommand()
    {
        if (! $this->isTracked) {
            return;
        }

        $eventResults[] = ['command' => 'php artisan '.$this->command,
                           'schedule' => $this->getCronSchedule($this->command),
                           'time' => round(microtime(true) - $this->start, 4)];

        app('Eyewitness\Eye\Eye')->api()->sendSchedulerPing($eventResults);
    }

    /**
     * Get the configured cron schedule for this command.
     *
     * @return string
     */
    protected function getCronSchedule($command)
    {
        $events = $this->getScheduledEvents();

        return $events[$command];
    }
}
