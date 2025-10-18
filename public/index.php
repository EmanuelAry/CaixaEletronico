<?php
require_once '../config/config.php';
require_once '../src/core/Router.php';
require_once '../src/core/Database.php';
require_once '../src/core/Log.php';
require_once '../src/core/Notification.php';

session_start();

// Inicializar o roteador e definir rotas
$router = new Router();
$router->add('', ['controller' => 'Auth', 'action' => 'login']);
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);
$router->add('dashboard', ['controller' => 'Account', 'action' => 'dashboard']);
$router->add('withdraw', ['controller' => 'Account', 'action' => 'withdraw']);
$router->add('deposit', ['controller' => 'Account', 'action' => 'deposit']);

$url = $_SERVER['QUERY_STRING'];
$router->dispatch($url);