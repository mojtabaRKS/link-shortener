<?php

namespace Core;

class Kernel
{
    public static function run()
    {
        Logger::enableSystemLogs();
    }
}