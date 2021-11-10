<?php

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Token;

/**
 * Tokens Controller
 *
 * Handles granting and revoking tokens
 */
class TokensController extends BaseController
{
    /**
     * Grant access to a token
     *
     * @param Request $request
     * @return Response
     */
    public function grant(Request $request): Response
    {
        if ($validate = $this->validateInput($request)) {
            return $validate;
        }

        $inputToken = $request->input('token');

        if (empty($inputToken)) {
            return new Response(400, 'No token provided');
        }

        (new Token($inputToken))->grant();

        return $this->successJson();
    }

    /**
     * Revoke access to a token
     *
     * @param Request $request
     * @return Response
     */
    public function revoke(Request $request): Response
    {
        if ($validate = $this->validateInput($request)) {
            return $validate;
        }
        $inputToken = $request->input('token');

        if (empty($inputToken)) {
            return new Response(400, 'No token provided');
        }

        (new Token($inputToken))->revoke();

        return $this->successJson();
    }

    protected function validateInput(Request $request): ?Response
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

        $inputToken = $request->input('token');

        if (empty($inputToken)) {
            return new Response(
                422,
                ['success' => false, 'message' => 'No token provided'],
                ['Content-Type' => 'application/json']
            );
        }

        if (!is_string($inputToken)) {
            return new Response(
                422,
                ['success' => false, 'message' => 'Invalid token provided'],
                ['Content-Type' => 'application/json']
            );
        }

        return null;
    }
}
