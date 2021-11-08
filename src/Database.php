<?php

namespace App;

use PDO;

/**
 * Database Class
 *
 * Handles all database communication
 */
class Database
{
    /** @var PDO */
    protected static $conn = null;

    /**
     * Run a query and return the results
     *
     * @param string $query
     * @param array<string,mixed> $params
     * @return array<mixed> Resulted rows
     */
    public static function query(string $query, array $params = []): ?array
    {
        self::init();

        $stmt = self::$conn->prepare($query);
        $stmt->execute($params);

        $result = $stmt->fetchAll();
        return $result === false ? null : $result;
    }

    /**
     * Inialize the database connection
     * if it's not already initialized
     *
     * @return void
     */
    protected static function init(): void
    {
        if (self::$conn === null) {
            self::$conn = new PDO(
                $_ENV['DB_DSN'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
                array(PDO::ATTR_PERSISTENT => true)
            );
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }
}
