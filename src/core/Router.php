<?php
namespace app\core;

use app\contracts\core\ILogger;
use app\core\Logger;
use app\core\Notification;
use app\core\Database;

class Router {
    private $routes = [];
    private $logger;
    private $database;
    private $notification;

    public function __construct(ILogger $logger) {
        $this->logger = $logger;
        $this->database = new Database();
        $this->notification = new Notification();
    }

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->normalizePath($path),
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        $requestPath = parse_url($requestUri, PHP_URL_PATH);
        $requestPath = $this->normalizePath($requestPath);


        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestPath)) {
                $this->executeRoute($route);
                return;
            }
        }

        // Rota não encontrada
        $this->handleNotFound();
    }

    private function matchRoute($route, $method, $path) {
        if ($route['method'] !== $method) {
            return false;
        }

        // Match exato para rotas simples
        if ($route['path'] === $path) {
            return true;
        }

        // Match com parâmetros (ex: /conta/{id})
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
        $routePattern = str_replace('/', '\/', $routePattern);
        $routePattern = '/^' . $routePattern . '$/';

        return preg_match($routePattern, $path);
    }

    private function executeRoute($route) {
        try {
            $controllerClass = "app\\controllers\\" . $route['controller'];
            $action = $route['action'];

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} não encontrado");
            }

            // Resolve dependências baseado no controller
            $controller = $this->resolveController($controllerClass);
            
            if (!method_exists($controller, $action)) {
                throw new \Exception("Método {$action} não encontrado em {$controllerClass}");
            }

            // Extrai parâmetros da URL
            $params = $this->extractParams($route);
            
            
            // Chama o método do controller
            call_user_func_array([$controller, $action], $params);

        } catch (\Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    private function resolveController($controllerClass) {
        // Resolve as dependências específicas de cada controller
        switch ($controllerClass) {
            case 'app\\controllers\\ContaController':
                $contaDao = new \app\dao\ContaDao($this->database);
                $contaModel = new \app\models\ContaModel(0, '', 0); // Model vazio inicialmente
                return new $controllerClass($contaModel, $contaDao, $this->logger, $this->notification);
            
            case 'app\\controllers\\CaixaEletronicoController':
                $caixaDao = new \app\dao\CaixaEletronicoDao($this->database);
                $caixaModel = new \app\models\CaixaEletronicoModel($caixaDao);
                return new $controllerClass($caixaModel, $caixaDao, $this->logger, $this->notification);
            
            default:
                return new $controllerClass();
        }
    }

    private function extractParams($route) {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = parse_url($requestUri, PHP_URL_PATH);
        $requestPath = $this->normalizePath($requestPath);

        $routePath = $route['path'];
        
        // Se for match exato, não há parâmetros
        if ($routePath === $requestPath) {
            return [];
        }

        // Extrai nomes dos parâmetros da rota
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        $paramNames = $paramNames[1];

        // Cria pattern para extrair valores
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $routePattern = str_replace('/', '\/', $routePattern);
        $routePattern = '/^' . $routePattern . '$/';

        preg_match($routePattern, $requestPath, $matches);
        array_shift($matches); // Remove o match completo

        $params = [];
        foreach ($paramNames as $index => $name) {
            if (isset($matches[$index])) {
                $params[] = $matches[$index];
            }
        }

        return $params;
    }

    private function normalizePath($path) {
        $path = trim($path, '/');
        return $path === '' ? '/' : '/' . $path;
    }

    private function handleNotFound() {
        http_response_code(404);
        echo "Página não encontrada";
    }

    private function handleError($message) {
        http_response_code(500);
        echo "Erro interno do servidor";
    }

    // Métodos auxiliares para definir rotas comuns
    public function setupDefaultRoutes() {
        // Rotas para ContaController
        $this->addRoute('GET', '/contas', 'ContaController', 'listarContas');
        $this->addRoute('GET', '/conta/{id}', 'ContaController', 'alternarConta');
        $this->addRoute('POST', '/conta/{id}/saque', 'ContaController', 'realizarSaque');
        $this->addRoute('POST', '/conta/{id}/deposito', 'ContaController', 'realizarDeposito');
        $this->addRoute('POST', '/conta', 'ContaController', 'criarConta');

        // Rotas para CaixaEletronicoController
        $this->addRoute('GET', '/saldo', 'CaixaEletronicoController', 'verSaldo');
        $this->addRoute('GET', '/extrato', 'CaixaEletronicoController', 'verExtrato');
    }
}