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
     */
    public function successJson(string $message = null): Response
    {
        return new Response(
            200,
            ['success' => true, 'message' => $message],
            ['Content-Type' => 'application/json']
        );
    }
}
