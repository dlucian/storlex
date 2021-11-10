<?php

namespace App\Traits;

use App\Request;
use App\Response;

trait ValidatesAdminRequests
{
    /**
     * Checks if the $request contains valid credentials
     * for an Admin request
     *
     * Use with:
     *
     * if ($adminRequest = $this->validateAdminRequest($request)) {
     *     return $adminRequest;
     * }
     *
     * @param Request $request
     * @return Response|null false if valid, Response if invalid
     */
    protected function validateAdminRequest(Request $request)
    {
        if (empty($request->server('HTTP_AUTHORIZATION'))) {
            return new Response(401, 'No authorization header provided');
        }

        if (substr($request->server('HTTP_AUTHORIZATION'), 0, 6) !== 'Bearer') {
            return new Response(401, 'Invalid authorization header');
        }

        if (empty($_ENV['ADMIN_TOKEN'])) {
            return new Response(500, 'No allowed tokens configured');
        }

        if (!in_array(substr($request->server('HTTP_AUTHORIZATION'), 7), $_ENV['ADMIN_TOKEN'])) {
            return new Response(401, 'Invalid token');
        }

        return null;
    }
}
