<?php

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('UTC');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


$router = new App\Router(dirname(__DIR__) . '/views');

$router
  ->get('/', 'home/index', 'home.index')
  ->get('/login', 'home/login', 'home.login')
  ->post('/login', 'home/login', 'home.login.post') // même vue, nom différent
  ->get('/register', 'home/register', 'home.register')
  ->run();

$routerAdmin = new App\Router(dirname(__DIR__) . '/views');

$routerAdmin
  ->get('/admin/[*:username]-[i:id]', 'admin/index', 'admin.index')
  ->get('/admin/[*:username]-[i:id]/flux-trafic', 'admin/trafic', 'admin.trafic')
  ->get('/admin/[*:username]-[i:id]/abonnes', 'admin/abonnes', 'admin.abonnes')
  ->get('/admin/[*:username]-[i:id]/historiques', 'admin/historiques', 'admin.historiques')
  ->get('/admin/[*:username]-[i:id]/operateurs', 'admin/operateurs', 'admin.operateurs')
  ->get('/admin/[*:username]-[i:id]/rapports', 'admin/rapports', 'admin.rapports')
  ->get('/admin/[*:username]-[i:id]/parametres', 'admin/parametres', 'admin.parametres')
  ->run('adminLayout');

$routerOperator = new App\Router(dirname(__DIR__) . '/views');

$routerOperator
  ->get('/operator/[*:username]-[i:id]', 'operator/index', 'operator.index')
  ->get('/operator/[*:username]-[i:id]/caisse', 'operator/caisse', 'operator.caisse')
  ->get('/operator/[*:username]-[i:id]/incident', 'operator/incident', 'operator.incident')
  ->get('/operator/[*:username]-[i:id]/mon-shift', 'operator/shift', 'operator.shift')
  ->run('operatorLayout');

http_response_code(404);
require dirname(__DIR__) . '/views/errors/404.php';
exit;

  // ->get('/blog/category/[*:slug]-[i:id]', 'category/show', 'category')
// ->get('/blog/[*:slug]-[i:id]', 'post/show', 'post')