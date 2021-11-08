<?php

namespace App;

use PDO;

class Token
{
    public function grant(string $token): void
    {
        $pdo = new PDO(
            'sqlite::memory:',
            null,
            null,
            array(PDO::ATTR_PERSISTENT => true)
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL)');
        $statement = $pdo->prepare('INSERT INTO tokens (token) VALUES (:token)');
        $statement->execute([
                'token' => $token,
            ]);
    }

    public function isValid(string $token): bool
    {
        $pdo = new PDO(
            'sqlite::memory:',
            null,
            null,
            array(PDO::ATTR_PERSISTENT => true)
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $pdo->prepare('SELECT * FROM tokens WHERE token = :token');
        $statement->execute([ 'token' => $token ]);

        $existingToken = $statement->fetchAll(PDO::FETCH_OBJ);
        return !empty($existingToken) && $existingToken[0]->token = $token;
    }
}
