<?php

namespace Eyewitness\Eye\Http\Middleware;

use Response;
use Config;

class EnabledQueueRoute {

    public function filter($route, $request)
    {
        if (! Config::get('eye::routes_queue')) {
            return Response::json(['error' => 'The queue route is disabled on the server'], 405)
                           ->setCallback($request->input('callback'));
        }
    }

}
