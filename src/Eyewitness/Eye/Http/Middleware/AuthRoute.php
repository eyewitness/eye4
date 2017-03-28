<?php

namespace Eyewitness\Eye\Http\Middleware;

use Response;
use Config;

class AuthRoute {

    public function filter($route, $request)
    {
        if (($request->get('app_token') !== Config::get('eye::app_token')) || ($request->get('secret_key') !== Config::get('eye::secret_key'))) {
            return Response::json(['error' => 'Unauthorized'], 401)
                           ->setCallback($request->input('callback'));
        }
    }

}
