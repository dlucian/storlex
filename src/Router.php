<?php

namespace App;

/**
 * Class Router
 *
 * @package Storlex
 * @author  Lucian Daniliuc <dlucian@gmail.com>
 * @version 1.0
 */
class Router
{
    /**
     * @var array<string, array<string, string>>
     */
    protected $routes = [];

    public function get(string $path, string $class): void
    {
        $this->addRoute('GET', $path, $class);
    }

    protected function addRoute(string $method, string $path, string $class): void
    {
        $this->routes[$method][$path] = $class;
    }

    public function match(string $path): ?string
    {
        foreach ($this->routes as $method => $routes) {
            if (array_key_exists($path, $routes)) {
                return $routes[$path];
            }
        }
        return null;
    }
}
