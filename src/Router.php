<?php

namespace App;

use Closure;

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
     * @var array<string, array<string, Closure>>
     */
    protected $routes = [];

    /**
     * Add a GET route for $path to $handler
     */
    public function get(string $path, Closure $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    protected function addRoute(string $method, string $path, Closure $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function match(string $path): ?Closure
    {
        $path = (string)filter_var($path, FILTER_SANITIZE_URL);
        foreach ($this->routes as $method => $routes) {
            if (array_key_exists($path, $routes)) {
                return $routes[$path];
            }
            foreach ($routes as $pathSearch => $handler) {
                $pathRegexp = preg_replace('/({)([a-zA-Z0-9]+)(})/', '(?<$2>.+)', $pathSearch);
                if (preg_match('#^' . $pathRegexp . '$#', $path)) {
                    return $handler;
                }
            }
        }
        return null;
    }

    public function handle(string $url): string
    {
        $handler = $this->match($url);
        return $handler ? $handler() : '404';
    }
}
