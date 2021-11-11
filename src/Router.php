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
     * Add a DELETE route for $path to $handler
     *
     * @param string $path
     * @param Closure $handler
     * @return void
     */
    public function delete(string $path, Closure $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
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
     * @return array<string,string|Closure|array<string,string|int>>|null
     */
    public function match(string $path, string $method = 'GET'): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        if (array_key_exists($path, $this->routes[$method])) {
            return [
                'callback' => $this->routes[$method][$path],
                'route' => $path,
                'params' => []
            ];
        }

        foreach ($this->routes[$method] as $pathSearch => $handler) {
            if ($params = $this->matchRouteWithParameters($path, $pathSearch)) {
                return [
                    'callback' => $handler,
                    'route' => $pathSearch,
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Match a route with named parameters
     *
     * @param string $path The path to match
     * @param string $pathSearch The path to search for
     * @return array<string,string>|null The parameters or null if no match
     */
    protected function matchRouteWithParameters(string $path, string $pathSearch): ?array
    {
        $matches = [];
        $pathRegexp = preg_replace('/({)([a-zA-Z0-9]+)(})/', '(?<$2>.+)', $pathSearch);
        if (!preg_match_all('#^' . $pathRegexp . '$#', $path, $matches)) {
            return null;
        }

        return $this->extractParameters($matches);
    }

    /**
     * Extract the parameters from the matches
     *
     * @param array<string,array<int,string>> $matches
     * @return array<string,string>
     */
    protected function extractParameters(array $matches): array
    {
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            $params[$key] = urldecode($value[0]);
        }
        return $params;
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
            return new Response(400, 'Bad request', ['Content-Type' => 'application/json']);
        }

        $matched = $this->match($parsedUrl['path'], $method);
        if ($matched === null) {
            return new Response(404, 'Not found', ['Content-Type' => 'application/json']);
        }
        if (!is_callable($matched['callback'])) {
            return new Response(500, 'Internal server error', ['Content-Type' => 'application/json']);
        }
        return $matched['callback']($matched['params']);
    }
}
