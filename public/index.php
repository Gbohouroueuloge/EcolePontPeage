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
  ->post('/login', 'home/login', 'home.login.post')
  ->get('/register', 'home/register', 'home.register')
  ->post('/register', 'home/register', 'home.register.post')
  ->run();

$routerAdmin = new App\Router(dirname(__DIR__) . '/views');

$routerAdmin
  ->get('/admin', 'admin/index', 'admin.index')
  ->get('/admin/flux-trafic', 'admin/trafic', 'admin.trafic')
  ->get('/admin/abonnes', 'admin/abonnes', 'admin.abonnes')
  ->get('/admin/historiques', 'admin/historiques', 'admin.historiques')
  ->get('/admin/operateurs', 'admin/operateurs', 'admin.operateurs')
  ->get('/admin/operateurs/[*:operateur_username]-[i:operateur_id]', 'admin/operateurs', 'admin.operateurs.details')
  ->get('/admin/rapports', 'admin/rapports', 'admin.rapports')
  ->get('/admin/parametres', 'admin/parametres', 'admin.parametres')
  ->run('adminLayout');

$routerOperator = new App\Router(dirname(__DIR__) . '/views');

$routerOperator
  ->get('/operator', 'operator/index', 'operator.index')
  ->get('/operator/caisse', 'operator/caisse', 'operator.caisse')
  ->get('/operator/incident', 'operator/incident', 'operator.incident')
  ->get('/operator/mon-shift', 'operator/shift', 'operator.shift')
  ->run('operatorLayout');

http_response_code(404);
require dirname(__DIR__) . '/views/errors/404.php';
exit;

  // ->get('/blog/category/[*:slug]-[i:id]', 'category/show', 'category')
// ->get('/blog/[*:slug]-[i:id]', 'post/show', 'post')