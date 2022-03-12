<?php

namespace Core;

class Response
{
    private $statusCode = 200;

    public function setCode(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }
    
    public function toJSON($data = [])
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}