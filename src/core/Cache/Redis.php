<?php

namespace Core\Cache;

use Closure;

class Redis
{
    private $redis;

    public function __construct()
    {
        $this->redis = RedisConnection::getInstance()->getConnection();
    }

    public static function __callStatic($name, $arguments)
    {
        return (new self)->$name(...$arguments);
    }

    public function set(string $key, $value, int $ttl = 0)
    {
        $this->redis->set($key, $value);

        if ($ttl > 0) {
            $this->redis->expire($key, $ttl);
        }
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function delete(string $key)
    {
        $this->redis->del($key);
    }

    public function flush()
    {
        $this->redis->flushAll();
    }

    public function remember(string $key, Closure $value, int $ttl = 0)
    {
        if ($this->redis->exists($key)) {
            return $this->get($key);
        }

        $this->set($key, call_user_func($value), $ttl);

        return $value;
    }

    public function exists(string $key)
    {
        return $this->redis->exists($key);
    }
}