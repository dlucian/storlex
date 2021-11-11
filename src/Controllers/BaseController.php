<?php

namespace App\Controllers;

use App\Response;

/**
 * Base controller
 *
 * Boilerplate for all controllers.
 */
abstract class BaseController
{
    /**
     * Generic success response
     *
     * @param string $message Optional success message
     * @return Response
     */
    protected function successJson(string $message = null): Response
    {
        return new Response(
            200,
            ['success' => true, 'message' => $message],
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Generic error message
     *
     * @param int $code
     * @param string $message
     * @return Response
     */
    protected function errorJson(int $code, string $message = null): Response
    {
        return new Response(
            $code,
            ['success' => false, 'message' => $message],
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Return a 404 response
     *
     * @return Response
     */
    protected function error404(): Response
    {
        return $this->errorJson(404, 'Not found');
    }
}
