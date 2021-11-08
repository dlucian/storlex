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
    /**
     * Grant access to $token
     *
     * @param string $token
     * @return void
     */
    public function grant(string $token): void
    {
        if ($this->isValid($token)) {
            return; // Already granted
        }
        Database::execute('INSERT INTO tokens (token) VALUES (:token)', [ 'token' => $token ]);
    }

    /**
     * Remoke access to $token
     *
     * @param string $token
     * @return void
     */
    public function revoke(string $token): void
    {
        Database::execute('DELETE FROM tokens WHERE token = :token', [ 'token' => $token ]);
    }

    /**
     * Check if $token is valid
     *
     * @param string $token Token to check
     * @return bool
     */
    public function isValid(string $token): bool
    {
        $results = Database::query('SELECT * FROM tokens WHERE token = :token', [ 'token' => $token ]);
        return !empty($results) && $results[0]['token'] = $token;
    }
}
