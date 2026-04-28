<?php

use App\Auth;
use App\ConnectionBDD;

$auth = new Auth(ConnectionBDD::getPdo());

if ($auth->isConnected()) {
  header('Location: ' . $auth->getRedirectPath());
  exit();
}

header('Location: ' . $router->url('home.login'));
exit;
