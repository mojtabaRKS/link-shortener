<?php

namespace Core\Cache;

class RedisFacade
{
    public static function getFacadeAccessor()
    {
        return new Redis;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::getFacadeAccessor(), $name], $arguments);
    }
}