<?php

use Dotenv\Dotenv;
use App\Models\User;
use Core\Logging\Logger;
use Core\Database\Connection;

require_once __DIR__ . '/vendor/autoload.php';

$schema = file_get_contents(__DIR__ . '/schema.sql');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

Logger::enableSystemLogs();
$logger = Logger::getInstance();

$database = Connection::getInstance();

$database->getConnection()->prepare($schema)->execute();

echo "Database schema created successfully." . PHP_EOL;

$user = (new User)->save([
    'name' => 'admin',
    'email' => 'admin@admin.com',
    'password' => hash('sha256', 'admin@12345'),
]);

echo "Admin user created successfully." . PHP_EOL;
