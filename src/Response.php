<?php

namespace App;

class Response
{
    /** @var int $statusCode */
    protected int $statusCode;

    /** @var string $body */
    protected string $body;

    /** @var array<string, string> $headers */
    protected array $headers;

    /**
     * Construct a Response object
     *
     * @param int $statusCode The status code
     * @param string|array<mixed> $body The body
     * @param array<string, string> $headers The headers
     */
    public function __construct(int $statusCode, $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        if (is_array($body)) {
            $this->body = json_encode($body) ? : '';
        } else {
            $this->body = $body;
        }
    }

    /**
     * Render the response to the browser, including
     * the status code, headers, and body
     *
     * @param Response $response The response to render
     * @return void
     */
    public static function render(self $response): void
    {
        http_response_code($response->statusCode);
        foreach ($response->headers as $name => $value) {
            header("$name: $value");
        }
        echo $response->body;
    }

    /**
     * Return the status code
     *
     * @return int HTTP status code to return
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Return the body of the response
     *
     * @return string The body of the response
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
