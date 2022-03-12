<?php

namespace Core;

use PDO;

// Singleton to connect db.
class Database
{
    // Hold the class instance.
    private static $instance = null;
    private $conn;

    // The db connection is established in the private constructor.
    private function __construct()
    {
        $this->conn = new PDO(
            "mysql:host={$_ENV['DB_HOST']};
            dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Cloning and unserialization are not permitted for singletons.
     */
    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
