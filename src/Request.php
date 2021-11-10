<?php

namespace App;

class Request
{
    /** @var array<string,string> */
    protected $server;

    /** @var array<string,string> */
    protected $get;

    /** @var array<string,string> */
    protected $post;

    /** @var array<string,string> */
    protected $cookie;

    /** @var array<string,array<string,string|int>> */
    protected $files;

    /**
     * @param array<string,string> $server
     * @param array<string,string> $get
     * @param array<string,string> $post
     * @param array<string,string> $cookie
     * @param array<string,array<string,string|int>> $files
     */
    public function __construct(
        array $server = [],
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $files = []
    ) {
        $this->server = $server ?: $_SERVER;
        $this->get = $get ?: $_GET;
        $this->post = $post ?: $_POST;
        $this->cookie = $cookie ?: $_COOKIE;
        $this->files = $files ?: $_FILES;

        $this->handleJsonBody();
    }

    /**
     * Check if we actually have received JSON input,
     * in which case we need to get the raw body
     * and parse the JSON
     *
     * @return void
     */
    protected function handleJsonBody(): void
    {
        if (
            empty($this->post) &&
            !empty($this->server['CONTENT_TYPE']) &&
            strpos($this->server['CONTENT_TYPE'], 'application/json') !== false
        ) {
            $input = file_get_contents('php://input');
            if (is_string($input)) {
                // @phpstan-ignore-next-line
                $this->post = json_decode($input, true);
            }
        }
    }

    /**
     * @return array<string,string>|string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Return a $_SERVER value
     *
     * @param string $attribute
     * @return string
     */
    public function server(string $attribute = ''): string
    {
        return $this->server[$attribute] ?? '';
    }

    /**
     * @return array<string,string>
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @return array<string,string>
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @return array<string,string>
     */
    public function getCookie(): array
    {
        return $this->cookie;
    }

    /**
     * @return array<string,array<string,string|int>>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param string $file
     * @return array<string,string|int>
     */
    public function getFile(string $file): array
    {
        return [
            'name' => $this->files[$file]['name'],
            'file' => $this->files[$file]['tmp_name'],
            'type' => $this->files[$file]['type'],
            'size' => $this->files[$file]['size'],
        ];
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function input(string $key, string $default = null): ?string
    {
        if (isset($this->post[$key])) {
            return Sanitize::string($this->post[$key]);
        }
        if (isset($this->get[$key])) {
            return Sanitize::string($this->get[$key]);
        }
        return $default;
    }
}
