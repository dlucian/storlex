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
     *
     * @param string $path
     * @param Closure $handler
     * @return void
     */
    public function get(string $path, Closure $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add a POST route for $path to $handler
     *
     * @param string $path
     * @param Closure $handler
     * @return void
     */
    public function post(string $path, Closure $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add a route for $method and $path to $handler
     *
     * @param string $method
     * @param string $path
     * @param Closure $handler
     * @return void
     */
    protected function addRoute(string $method, string $path, Closure $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    /**
     * Get the handler for the route matching $method and $path
     *
     * @param string $path
     * @param string $method
     * @return Closure|null
     */
    public function match(string $path, string $method = 'GET'): ?Closure
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        if (array_key_exists($path, $this->routes[$method])) {
            return $this->routes[$method][$path];
        }

        foreach ($this->routes[$method] as $pathSearch => $handler) {
            $pathRegexp = preg_replace('/({)([a-zA-Z0-9]+)(})/', '(?<$2>.+)', $pathSearch);
            if (preg_match('#^' . $pathRegexp . '$#', $path)) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * Get the handler for the route matching $method and $path
     *
     * @param string $method HTTP method
     * @param string $url URL
     * @return Response
     */
    public function handle(string $method, string $url): Response
    {
        $parsedUrl = parse_url(Sanitize::url($url));

        if ($parsedUrl === false || !array_key_exists('path', $parsedUrl)) {
            return new Response(400, 'Bad request');
        }

        $handler = $this->match($parsedUrl['path'], $method);

        return $handler ? $handler() : new Response(404, 'Not found');
    }
}
