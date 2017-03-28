<?php

namespace Eyewitness\Eye\Http\Middleware;

use Response;
use Config;

class EnabledComposerRoute {

    public function filter($route, $request)
    {
        if (! Config::get('eye::monitor_composer_lock')) {
            return Response::json(['error' => 'The composer route is disabled on the server'], 405)
                           ->setCallback($request->input('callback'));
        }
    }

}
