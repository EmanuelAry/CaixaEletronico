<?php
require_once __DIR__ . '/../src/core/autoload.php';

use app\core\Router;
use app\core\Logger;
use app\core\Database;
use app\core\Notification;

// Inicializa dependências
$logger = new Logger();
$database = new Database();
$notification = new Notification();

// Configura o router
$router = new Router($logger, $database, $notification);
$router->setupDefaultRoutes();

// Dispatch da rota
$router->dispatch();