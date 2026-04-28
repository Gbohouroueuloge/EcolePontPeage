<?php

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('UTC');

$sessionPath = dirname(__DIR__) . '/var/sessions';
if (!is_dir($sessionPath)) {
  mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new App\Router(dirname(__DIR__) . '/views');

$router
  ->get('/', 'home/index', 'home.index')
  ->get('/login', 'home/login', 'home.login')
  ->post('/login', 'home/login', 'home.login.post')
  ->get('/attente', 'home/waiting', 'home.waiting')
  ->run();

$routerAdmin = new App\Router(dirname(__DIR__) . '/views');

$routerAdmin
  ->get('/admin', 'admin/index', 'admin.index')
  ->get('/admin/analytics', 'admin/analytics', 'admin.analytics')
  ->get('/admin/tarifs', 'admin/tarifs', 'admin.tarifs')
  ->post('/admin/tarifs', 'admin/tarifs', 'admin.tarifs.post')
  ->get('/admin/abonnes', 'admin/abonnes', 'admin.abonnes')
  ->get('/admin/historiques', 'admin/historiques', 'admin.historiques')
  ->get('/admin/operateurs', 'admin/operateurs', 'admin.operateurs')
  ->get('/admin/operateurs/[*:operateur_username]-[i:operateur_id]', 'admin/operateurs', 'admin.operateurs.details')
  ->get('/admin/utilisateurs', 'admin/users', 'admin.users')
  ->post('/admin/utilisateurs', 'admin/users', 'admin.users.post')
  ->get('/admin/messages', 'admin/messages', 'admin.messages')
  ->get('/admin/rapports', 'admin/rapports', 'admin.rapports')
  ->get('/admin/parametres', 'admin/parametres', 'admin.parametres')
  ->post('/admin/parametres', 'admin/parametres', 'admin.parametres.post')
  ->run('adminLayout');

$routerOperator = new App\Router(dirname(__DIR__) . '/views');

$routerOperator
  ->get('/operator', 'operator/index', 'operator.index')
  ->post('/operator', 'operator/index', 'operator.index.post')
  ->get('/operator/caisse', 'operator/caisse', 'operator.caisse')
  ->get('/operator/incident', 'operator/incident', 'operator.incident')
  ->post('/operator/incident', 'operator/incident', 'operator.incident.post')
  ->get('/operator/mon-dashboard', 'operator/shift', 'operator.shift')
  ->run('operatorLayout');

$routerSupervisor = new App\Router(dirname(__DIR__) . '/views');

$routerSupervisor
  ->get('/superviseur', 'supervisor/index', 'supervisor.index')
  ->get('/superviseur/equipe', 'supervisor/team', 'supervisor.team')
  ->get('/superviseur/incidents', 'supervisor/incidents', 'supervisor.incidents')
  ->get('/superviseur/rapports', 'supervisor/reports', 'supervisor.reports')
  ->run('supervisorLayout');

http_response_code(404);
require dirname(__DIR__) . '/views/errors/404.php';
exit;
