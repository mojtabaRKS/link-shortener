<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController use JWT method to authenticate users
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $this->prepareData(json_decode($request->getContent(), true));
        $user = User::query()->where('email', $credentials['email'])
            ->where('password', $credentials['password'])->first();

        if (!$user instanceof User) {
            return $this->failureResponse('email or password is invalid', [], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->generateToken($user);

        return $this->successResponse(
            'token generated successfully',
            ['token' => $token],
            Response::HTTP_OK
        );
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
