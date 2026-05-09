<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\TollRepository;

class AuthController
{
    public function __construct(
        private TollRepository $repository,
        private Auth $auth,
    ) {
    }

    public function login(Request $request): void
    {
        $payload = $request->json();
        $email = trim((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($email === '' || $password === '') {
            Response::error('Email et mot de passe obligatoires.');
        }

        $user = $this->repository->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Response::error('Identifiants incorrects.', 401);
        }

        $token = $this->repository->createToken((int) $user['id']);

        Response::success([
            'token' => $token,
            'user' => $this->repository->publicUser($user),
        ]);
    }

    public function me(Request $request): void
    {
        $user = $this->auth->user($request);
        Response::success(['user' => $this->repository->publicUser($user)]);
    }

    public function logout(Request $request): void
    {
        $token = $request->bearerToken();
        if ($token) {
            $this->repository->deleteToken($token);
        }

        Response::success(['logged_out' => true]);
    }
}
