<?php

namespace Eyewitness\Eye\Api;

use GuzzleHttp\Client;
use Exception;
use Config;
use Log;

class LatestApi extends Api
{
    /**
     * Create a new Api instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return void
     */
    public function __construct()
    {
        $this->client = app(Client::class);

        parent::__construct();
    }

    /**
     * Install a new Eyewitness application. Will return the newly generated
     * app_token and secret_key from the API.
     *
     * @param  array  $setup
     * @return array
     */
    public function install($setup)
    {
        $this->headers['json'] = $setup;

        $response = $this->client->post($this->api.'/install', $this->headers);

        return json_decode($response->getBody());
    }

    /**
     * Run a check for the composer lock against the SensioLabs API.
     *
     * @return json
     */
    public function runComposerLockCheck()
    {
        $this->headers['headers'] = ['Accept' => 'application/json'];
        $this->headers['multipart'] = [['name' => 'lock', 'contents' => fopen(Config::get('eye::composer_lock_file_location'), 'r')]];

        $response = null;

        try {
            $response = $this->client->post('https://security.sensiolabs.org/check_lock', $this->headers);
            $response = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('SensioLabs Composer Lock check failed due to: '.$e->getMessage());
        }

        return $response;
    }

    /**
     * Send the ping notification to the Eyewitness.io API.
     *
     * @param  string  $route
     * @param  array   $data
     * @return void
     */
    protected function ping($route, $data = [])
    {
        if (! Config::get('eye::api_enabled')) {
            return;
        }

        $data['app_token'] = Config::get('eye::app_token');
        $data['secret_key'] = Config::get('eye::secret_key');

        $this->headers['json'] = $data;

        try {
            $this->client->post($this->api."/$route", $this->headers);
        } catch (Exception $e) {
            //
        }
    }
}
