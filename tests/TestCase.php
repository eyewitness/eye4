<?php

abstract class TestCase extends Orchestra\Testbench\TestCase
{
    /**
     * The base API route to use while testing the application.
     *
     * @var string
     */
    protected $api;

    /**
     * The app_token to use while testing the application.
     *
     * @var string
     */
    protected $app_token;

    /**
     * The secret_key to use while testing the application.
     *
     * @var string
     */
    protected $secret_key;

    /**
     * The auth string to authorize routes.
     *
     * @var string
     */
    protected $auth;

    /**
     * Setup the application.
     *
     * @return void
     */
    public function setUp()
    {
        if (! defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }

        parent::setUp();

        $this->app_token = Config::get('eye::app_token');
        $this->secret_key = Config::get('eye::secret_key');
        $this->auth = "?app_token=".$this->app_token.'&secret_key='.$this->secret_key;

        $this->api = 'api/eyewitness/v1/';
    }

    /**
     * Add the Eyewitness Service Provider to the bootstrapped application.
     *
     * @eturn array
     */
    protected function getPackageProviders()
    {
        return [Eyewitness\Eye\EyeServiceProvider::class];
    }


    public function tearDown()
    {
        Mockery::close();
    }

}
