<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = $this->generateToken($user);

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (User::where('email', $email)->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Email already registered']));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'client'
        ]);

        $token = $this->generateToken($user);

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    private function generateToken(User $user): string
    {
        $payload = [
            'uid' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + ($_ENV['JWT_EXPIRY'] ?? 3600)
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}
