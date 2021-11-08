<?php

namespace App;

use PDO;

class Token
{
    public function grant(string $token): void
    {
        Database::execute('INSERT INTO tokens (token) VALUES (:token)', [ 'token' => $token ]);
    }

    public function isValid(string $token): bool
    {
        $results = Database::query('SELECT * FROM tokens WHERE token = :token', [ 'token' => $token ]);
        return !empty($results) && $results[0]['token'] = $token;
    }
}
