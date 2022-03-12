<?php

namespace Core\Exceptions;

use Exception;

class MethodNotAllowedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Method NotAllowed', 405);
    }
}