<?php

namespace App\Controllers;

use Throwable;
use Core\Database\Connection;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $dbConnection;

    public function __construct()
    {
        $this->dbConnection = Connection::getInstance()->getConnection();
    }

    /**
     * @param string $message
     * @param array $data
     * @param int $code
     */
    public function successResponse(
        string $message,
        array $data = [],
        $code = Response::HTTP_OK
    ) {
        return new Response(
            json_encode([
                'status' => true,
                'message' => $message,
                'data' => $data,
            ]),
            $code,
            ['content-type' => 'text/json']
        );
    }

    /**
     * @param Throwable $excetion
     * @return int $code
     */
    public function failureResponse(
        Throwable $exception,
        $code = Response::HTTP_BAD_REQUEST
    ) {
        return new Response(
            json_encode([
                'status' => false,
                'message' => $exception->getMessage(),
                'data' => $_ENV['APP_DEBUG'] ? $exception->getTrace() : [],
            ]),
            $code,
            ['content-type' => 'text/json']
        );
    }
}
