<?php

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Token;
use App\Traits\ValidatesAdminRequests;

/**
 * Tokens Controller
 *
 * Handles granting and revoking tokens
 */
class TokensController extends BaseController
{
    use ValidatesAdminRequests;

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
            return $this->errorJson(400, 'No token provided');
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
            return $this->errorJson(400, 'No token provided');
        }

        (new Token($inputToken))->revoke();

        return $this->successJson();
    }

    /**
     * Validate the input
     *
     * @param Request $request
     * @return Response|null
     */
    protected function validateInput(Request $request): ?Response
    {
        if ($adminRequest = $this->validateAdminRequest($request)) {
            return $adminRequest;
        }

        $inputToken = $request->input('token');

        if (empty($inputToken)) {
            return $this->errorJson(422, 'No token provided');
        }

        if (!is_string($inputToken)) {
            return $this->errorJson(422, 'Invalid token provided');
        }

        return null;
    }
}
