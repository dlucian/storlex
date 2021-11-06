<?php

namespace App;

class Router
{
    public function get(string $path, string $class): string
    {
        return 'get';
    }

    public function match(string $path): string
    {
        return 'match';
    }
}
