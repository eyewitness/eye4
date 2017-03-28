<?php

namespace Eyewitness\Eye\Http\Controllers;

use Eyewitness\Eye\Eye;
use Controller;
use Response;

class ComposerController extends Controller
{
    /**
     * The eyewitness api.
     *
     * @var \Eyewitness\Eye\Api
     */
    protected $api;

    /**
     * Create a new ComposerController instance.
     *
     * @return void
     */
    public function __construct(Eye $eye)
    {
        $this->beforeFilter('eyewitness_composer_route');

        $this->api = $eye->api();
    }

    /**
     * Run the commposer.lock check and return the results.
     *
     * @return json
     */
    public function ping()
    {
        $result = $this->api->runComposerLockCheck();

        if (is_null($result)) {
            return Response::json(['error' => 'Could not run composer.lock check'], 500);
        }

        return Response::json($result, 200);
    }
}
