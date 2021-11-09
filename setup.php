<?php

/**
 * Setup database for the application.
 *
 * This substitutes a migration system support,
 * therefore it needs to be crafted in such a way
 * that it can be executed multiple times,
 * whitout the risk of breaking the application.
 */

use App\Database;

// Load bootstrap
require(__DIR__ . '/public/index.php');

Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL PRIMARY KEY)');

echo "Setup complete!";