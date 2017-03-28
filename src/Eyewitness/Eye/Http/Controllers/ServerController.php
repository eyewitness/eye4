<?php

namespace Eyewitness\Eye\Http\Controllers;

use Illuminate\Routing\Controller;
use Eyewitness\Eye\Eye;
use Response;

class ServerController extends Controller
{
    /**
     * The main eyewitness.
     *
     * @var \Eyewitness\Eye\Eye
     */
    protected $eye;

    /**
     * Create a new ServerController instance.
     *
     * @param \Eyewitness\Eye\Eye  $eye
     * @return void
     */
    public function __construct(Eye $eye)
    {
        $this->eye = $eye;
    }

    /**
     * Run the server ping command.
     *
     * @return json
     */
    public function ping()
    {
        return Response::json($this->eye->runAllChecks(), 200);
    }
}
