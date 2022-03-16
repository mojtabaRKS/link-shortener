<?php

namespace App\Traits;

use Exception;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

trait CheckAuth
{
    /**
     * @param Request $request
     * @return void
     */
    public function checkAuth(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        if (!$token) {
            throw new Exception('Token not found', 403);
        }
        
        $user = User::query()
            ->where('token', $token)
            ->first();

        if (!$user instanceof User) {
            throw new Exception('Invalid credentials', 401);
        }
    }
    
}