<?php

namespace Core\Cache;

use Predis\Client;

class RedisConnection
{
    // Hold the class instance.
    private static $instance = null;
    private $conn;

    // The db connection is established in the private constructor.
    private function __construct()
    {
        $this->conn = new Client(array(
            "scheme" => 'tcp',
            "host" => $_ENV['REDIS_HOST'],
            "port" => $_ENV['REDIS_PORT'],
            "password" => $_ENV['REDIS_PASSWORD']
        ));
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