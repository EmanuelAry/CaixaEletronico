<?php
require_once __DIR__ . '/../src/core/autoload.php';

use app\core\Router;
use app\core\Logger;

// Inicializa logger
$logger = new Logger();

// Configura o router
$router = new Router($logger);
$router->setupDefaultRoutes();

// Dispatch da rota
$router->dispatch();