<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\TollRepository;

class ApiController
{
    public function __construct(
        private TollRepository $repository,
        private Auth $auth,
    ) {
    }

    public function adminDashboard(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->adminDashboard());
    }

    public function operatorDashboard(Request $request): void
    {
        $user = $this->auth->requireRole($request, 'operateur');
        Response::success($this->repository->operatorDashboard((int) $user['id']));
    }

    public function tariffs(Request $request): void
    {
        $this->auth->user($request);
        Response::success($this->repository->allTariffs());
    }

    public function updateTariffs(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        $items = $request->json()['items'] ?? [];
        $this->repository->updateTariffs($items);
        Response::success(['updated' => true]);
    }

    public function operators(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->allOperators());
    }

    public function subscribers(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->subscribers());
    }

    public function createSubscriber(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        $subscriber = $this->repository->createSubscriber($request->json());
        Response::success($subscriber, 201);
    }

    public function renewSubscriber(Request $request, int $id): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->renewSubscriber($id));
    }

    public function history(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->history());
    }

    public function reports(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->reports());
    }

    public function settings(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->settings());
    }

    public function updateSettings(Request $request): void
    {
        $this->auth->requireRole($request, 'admin');
        Response::success($this->repository->updateSettings($request->json()));
    }

    public function incidents(Request $request): void
    {
        $this->auth->user($request);
        Response::success($this->repository->incidents());
    }

    public function createIncident(Request $request): void
    {
        $user = $this->auth->requireRole($request, 'operateur');
        Response::success($this->repository->createIncident($request->json(), (int) $user['id']), 201);
    }

    public function createPassage(Request $request): void
    {
        $user = $this->auth->requireRole($request, 'operateur');
        Response::success($this->repository->createPassage($request->json(), (int) $user['id']), 201);
    }
}
