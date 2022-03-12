<?php

namespace Core;

use Core\Logging\Logger;

class Kernel
{
    public function run()
    {
        Logger::enableSystemLogs();
    }
}