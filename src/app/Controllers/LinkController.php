<?php

namespace App\Controllers;

use Throwable;
use Core\Database;
use App\Models\Link;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends Controller
{
    protected $dbConnection;
    public function __construct()
    {
        $this->dbConnection = Database::getInstance()->getConnection();
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->dbConnection->beginTransaction();

        try {
            $data = $this->prepareData(json_decode($request->getContent(), true));
            (new Link)->save($data);
            $this->dbConnection->commit();

            return $this->successResponse(
                'Link created successfully', 
                [], 
                Response::HTTP_CREATED
            );

        } catch (Throwable $exception) {
            $this->dbConnection->rollBack();
            return $this->failureResponse($exception);
        }
    }

    /**
     * @param array $data
     */
    public function prepareData(array $data)
    {
        $exploded = explode('/', $data['url']);
        $domain = $exploded[0];
        unset($exploded[0]);
        $url = implode('/', $exploded);

        return [
            'domain' => $domain,
            'url' => $url,
        ];
    }
}
