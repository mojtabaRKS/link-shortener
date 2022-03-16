<?php

namespace App\Controllers;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use DateTimeImmutable;
use App\Traits\CheckAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController use JWT method to authenticate users
 */
class AuthController extends Controller
{
    use CheckAuth;

    public function login(Request $request)
    {
        try {
            $credentials = $this->prepareData(json_decode($request->getContent(), true));

        $user = User::query()
            ->where('email', $credentials['email'])
            ->where('password', $credentials['password'])
            ->first();
        
        if (!$user instanceof User) {
            throw new Exception('Invalid credentials', 401);
        }

        $token = $this->generateToken($user);

        $user->update($user->id,['token' => $token]);
        
        return $this->successResponse(
            'token generated successfully',
            ['token' => $token],
            Response::HTTP_OK
        );
        } catch (\Throwable $th) {
            return $this->failureResponse(
                $th,
                $th->getCode()
            );
        }
    }

    public function logout(Request $request)
    {
        $this->checkAuth($request);
        $user = $this->getUser($request);

        $user->update($user->id,['token' => null]);

        return $this->successResponse(
            'logged out successfully',
            [],
            Response::HTTP_OK
        );
    }

    private function getUser(Request $request)
    {
        $token = $request->headers->get('Authorization');
        $token = explode(' ', $token)[1];

        $user = User::query()
            ->where('token', $token)
            ->first();

        if (!$user instanceof User) {
            throw new Exception('Invalid credentials', 401);
        }
        
        return $user;
    }

    private function prepareData($data)
    {
        return [
            'email' => $data['email'],
            'password' => hash('sha256', $data['password']),
        ];
    }

    private function generateToken($user)
    {
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();
        $serverName = "http://link-shortener.com";

        $payload = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,                           // Expire
            'user' => ['id' => $user->id],
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}
