<?php

namespace App;

/**
 * Token handling class
 *
 * Used to generate, validate and delete tokens
 * that are used to authenticate users.
 */
class Token
{
    protected string $token;

    /**
     * Constructor
     *
     * @param string $token The token to use
     */
    public function __construct(string $token = '')
    {
        $this->token = $token ?: bin2hex(random_bytes(32));
    }

    /**
     * Grant access to $token
     *
     * @return void
     */
    public function grant(): void
    {
        if ($this->isValid()) {
            return; // Already granted
        }
        Database::execute('INSERT INTO tokens (token) VALUES (:token)', [ 'token' => $this->token ]);
    }

    /**
     * Remoke access to token
     *
     * @return void
     */
    public function revoke(): void
    {
        Database::execute('DELETE FROM tokens WHERE token = :token', [ 'token' => $this->token ]);
    }

    /**
     * Check if token is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $results = Database::query('SELECT * FROM tokens WHERE token = :token', [ 'token' => $this->token ]);
        return !empty($results) && $results[0]['token'] = $this->token;
    }
}
