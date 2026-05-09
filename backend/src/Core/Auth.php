<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\TollRepository;

class Auth
{
    public function __construct(private TollRepository $repository)
    {
    }

    public function user(Request $request): array
    {
        $token = $request->bearerToken();
        if (!$token) {
            Response::error('Authentification requise.', 401);
        }

        $user = $this->repository->getUserByToken($token);
        if (!$user) {
            Response::error('Session invalide.', 401);
        }

        return $user;
    }

    public function requireRole(Request $request, string $role): array
    {
        $user = $this->user($request);

        if (($user['role'] ?? null) !== $role) {
            Response::error('Acces refuse.', 403);
        }

        return $user;
    }
}
