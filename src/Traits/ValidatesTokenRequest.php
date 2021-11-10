<?php

namespace App\Traits;

use App\Request;
use App\Response;
use App\Token;

trait ValidatesTokenRequest
{
    /**
     * Checks if the $request contains a valid token
     *
     * Use with:
     *
     * if ($tokenRequest = $this->validateTokenRequest($request)) {
     *     return $tokenRequest;
     * }
     *
     * @param Request $request
     * @return Response|null false if valid, Response if invalid
     */
    protected function validateTokenRequest(Request $request)
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

        $token = substr($request->server('HTTP_AUTHORIZATION'), 7);

        if ((new Token($token))->isValid() === false) {
            return new Response(401, 'Invalid token');
        }

        return null;
    }
}
