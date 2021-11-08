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
     * Return the status code
     *
     * @return int HTTP status code to return
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
