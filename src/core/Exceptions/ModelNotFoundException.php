<?php

namespace Core\Exceptions;

class ModelNotFoundException extends \Exception
{
    public function __construct($id)
    {
        parent::__construct("Model with id [$id] not found", 404);
    }
}