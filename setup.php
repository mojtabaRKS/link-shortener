<?php

use Core\Database;
use App\Models\User;

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/bootstrap/app.php';

$schema = file_get_contents(__DIR__ . '/schema.sql');

$database = Database::getInstance();

$database->getConnection()->prepare($schema)->execute();

echo "Database schema created successfully." . PHP_EOL;

$user = (new User)->save([
    'name' => 'admin',
    'email' => 'admin@admin.com',
    'password' => hash('sha256', 'admin@12345'),
]);

echo "Admin user created successfully." . PHP_EOL;
