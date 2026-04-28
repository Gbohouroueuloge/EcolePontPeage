<?php

use App\Auth;
use App\ConnectionBDD;
use App\Services\AdminService;

$pdo = ConnectionBDD::getPdo();
$auth = new Auth($pdo);
$user = $auth->getUser();

if (!Auth::isConnected() || !$auth->isAdmin() || !$user) {
  http_response_code(401);
  header('Location: /login');
  exit();
}

$adminService = new AdminService($pdo);
$adminUnreadCount = $adminService->countUnreadNotifications();
