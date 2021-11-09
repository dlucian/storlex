<?php

namespace App\Controllers;

use App\Response;

class HomeController
{
    public function index(): Response
    {
        return new Response(
            200,
            'Storlex/API',
            ['Content-Type' => 'text/plain']
        );
    }
}
