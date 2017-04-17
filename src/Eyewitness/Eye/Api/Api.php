<?php

namespace Eyewitness\Eye\Api;

use Config;

abstract class Api
{
    /**
     * The GuzzleHttp client.
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * The Eyewitness.io API url.
     *
     * @var string
     */
    protected $api;

    /**
     * The default headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * Create a new Api instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return void
     */
    public function __construct()
    {
        $this->api = Config::get('eye::api_url');

        $this->headers = ['connect_timeout' => 15,
                          'timeout' => 15,
                          'debug' => false];
    }

    /**
     * Send a ping to request an installation email.
     *
     * @param  string  $email
     * @return void
     */
    public function sendInstallEmail($email)
    {
        $this->ping('install/email', ['email' => $email]);
    }

    /**
     * Send a ping for the queue.
     *
     * @param  string  $connection
     * @param  string  $tube
     * @return void
     */
    public function sendQueuePing($connection, $tube)
    {
        $this->ping('queue/ping', ['connection' => $connection, 'tube' => $tube]);
    }

    /**
     * Send a ping for the server.
     *
     * @param  array   $data
     * @return void
     */
    public function sendServerPing($data)
    {
        $this->ping('server/ping', ['data' => $data]);
    }

    /**
     * Send a ping for a failing queue.
     *
     * @param  string  $connection
     * @param  string  $job
     * @param  string  $tube
     * @param  string  $exception_class
     * @param  string  $exception_message
     * @return void
     */
    public function sendQueueFailingPing($connection, $job, $tube)
    {
        $this->ping('queue/failing', ['connection' => $connection,
                                      'job' => $job,
                                      'tube' => $tube]);
    }

    /**
     * Send a ping for a failing queue.
     *
     * @param  string  $connection
     * @param  string  $job
     * @param  string  $tube
     * @param  string  $exception_class
     * @param  string  $exception_message
     * @return void
     */
    public function sendExceptionAlert($level, $message)
    {
        $this->ping('log/exception', ['level' => $level,
                                      'message' => $message]);
    }

    /**
     * Send a ping for the scheduled events.
     *
     * @param  array  $events
     * @return void
     */
    public function sendSchedulerPing($events = null)
    {
        $this->ping('scheduler/ping', ['events' => $events]);
    }

    /**
     * Send a webhook ping to the server with a custom message.
     *
     * @param  string  $message
     * @return void
     */
    public function sendWebhookPing($message)
    {
        $this->ping('webhook/ping', ['message' => $message]);
    }

    /**
     * Send the ping that the server is going down for planned maintenance.
     *
     * @return void
     */
    public function down()
    {
        $this->ping('maintenance/down');
    }

    /**
     * Send the ping that the server is now up from planned maintenance.
     *
     * @return void
     */
    public function up()
    {
        $this->ping('maintenance/up');
    }

    /**
     * Install a new Eyewitness application. Will return the newly generated
     * app_token and secret_key from the API.
     *
     * @param  array  $setup
     * @return array
     */
    abstract public function install($setup);

    /**
     * Run a check for the composer lock against the SensioLabs API.
     *
     * @return json
     */
    abstract public function runComposerLockCheck();

    /**
     * Send the ping notification to the Eyewitness.io API.
     *
     * @param  string  $route
     * @param  array   $data
     * @return void
     */
    abstract protected function ping($route, $data = []);
}
