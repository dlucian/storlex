<?php

namespace App\Controllers;

use App\Response;
use App\Token;

class TokensController
{
    public function grant(string $token): Response
    {
        (new Token())->grant($token);
        return new Response(
            200,
            ['success' => true],
            ['Content-Type' => 'application/json']
        );
    }
}
