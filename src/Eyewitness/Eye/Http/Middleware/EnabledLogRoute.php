<?php

namespace Eyewitness\Eye\Http\Middleware;

use Response;
use Config;

class EnabledLogRoute {

    public function filter($route, $request)
    {
        if (! Config::get('eye::routes_log')) {
            return Response::json(['error' => 'The log route is disabled on the server'], 405)
                           ->setCallback($request->input('callback'));
        }
    }

}
