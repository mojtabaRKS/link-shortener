<?php

namespace App\Controllers;

use Throwable;
use App\Models\Link;
use App\Traits\CheckAuth;
use Core\Cache\Redis;
use Core\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends Controller
{
    use CheckAuth;
    
    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->checkAuth($request);
        $this->dbConnection->beginTransaction();

        try {
            $data = $this->prepareData(json_decode($request->getContent(), true));
            $link = (new Link)->save($data);
            
            Redis::set($link->short, $link->url, $_ENV['CACHE_TTL']);
            $this->dbConnection->commit();

            return $this->successResponse(
                'Link created successfully', 
                [], 
                Response::HTTP_CREATED
            );

        } catch (Throwable $exception) {
            dd($exception);
            $this->dbConnection->rollBack();
            return $this->failureResponse($exception);
        }
    }

    /**
     * @param Request $request
     */
    public function show($id, Request $request)
    {
        $this->checkAuth($request);
        try {
            $link = (new Link)->find($id);

            return $this->successResponse(
                'Link found successfully', 
                $link->toArray(), 
                Response::HTTP_OK
            );

        } catch (Throwable $exception) {
            return $this->failureResponse($exception);
        }
    }

    public function redirect(Request $request)
    {
        $code = trim($request->getPathInfo(), '/');

        if (Redis::exists($code)) {
            header('Location: ' . Redis::get($code));
            exit;
        }

        $link = Link::query()->where('short', $code)->first();

        if (!$link) {
            throw new ModelNotFoundException($code);
        }

        header('Location: ' . $link->original);
        exit;
    }

    public function update($id, Request $request)
    {
        $this->checkAuth($request);
        $this->dbConnection->beginTransaction();

        try {
            $data = $this->prepareData(json_decode($request->getContent(), true));
            (new Link)->update($id, $data);
            $link = Link::query()->find($id);
            Redis::set($link->short, $link->url, $_ENV['CACHE_TTL']);
            $this->dbConnection->commit();

            return $this->successResponse(
                'Link updated successfully', 
                [], 
                Response::HTTP_OK
            );

        } catch (Throwable $exception) {
            $this->dbConnection->rollBack();
            return $this->failureResponse($exception);
        }
    }

    public function delete($id, Request $request)
    {
        $this->checkAuth($request);
        $this->dbConnection->beginTransaction();

        try {
            $link = Link::query()->find($id);
            Redis::delete($link->short);
            (new Link)->delete($id);
            $this->dbConnection->commit();

            return $this->successResponse(
                'Link deleted successfully', 
                [], 
                Response::HTTP_OK
            );

        } catch (Throwable $exception) {
            $this->dbConnection->rollBack();
            return $this->failureResponse($exception);
        }
    }

    public function index(Request $request)
    {
        $this->checkAuth($request);
        $links = Link::query()->get();
        
        return $this->successResponse(
            'Links found successfully', 
            $links, 
            Response::HTTP_OK
        );
    }

    /**
     * @param array $data
     */
    private function prepareData(array $data)
    {
        $original = $data['url'];

        do {
            $short = substr(md5(uniqid()), 0, 6);
        } while (Link::query()->where('short', $short)->exists());

        return [
            'original' => $original,
            'short' => $short,
        ];
    }
}
