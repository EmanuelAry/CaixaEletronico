<?php
namespace app\core;

use app\contracts\core\ILogger;
use app\core\Logger;
use app\core\Notification;
use app\core\Database;
use app\services\CaixaEletronicoService;

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
                $caixaDao = new \app\dao\CaixaEletronicoDao($this->database);
                $caixaModel = new \app\models\CaixaEletronicoModel($caixaDao);
                $caixaService = new \app\services\CaixaEletronicoService($caixaModel, $caixaDao, $this->logger, $this->notification);
                $contaDao = new \app\dao\ContaDao($this->database);
                $contaModel = new \app\models\ContaModel(0, '', 0, ''); // Model vazio inicialmente
                $contaService = new \app\services\ContaService($contaModel, $contaDao, $this->logger, $this->notification, $caixaService);
                return new $controllerClass($contaService);
            
            case 'app\\controllers\\CaixaEletronicoController':
                $caixaDao = new \app\dao\CaixaEletronicoDao($this->database);
                $caixaModel = new \app\models\CaixaEletronicoModel($caixaDao);
                $caixaService = new \app\services\CaixaEletronicoService($caixaModel, $caixaDao, $this->logger, $this->notification);
                return new $controllerClass($caixaService);
            
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
         // Remove o base path se estiver presente
        $basePath = '/caixaeletronico/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
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
        // Pagina inicial
        $this->addRoute('GET', '/', 'ContaController', 'loginContaView');
        
        // Rotas para ContaController
        $this->addRoute('GET', '/conta/login',               'ContaController', 'loginContaView');
        $this->addRoute('GET', '/conta/criar',               'ContaController', 'criarContaView');
        $this->addRoute('GET', '/conta/menuCaixaView',       'ContaController', 'menuCaixaView');
        $this->addRoute('get', '/conta/logoutAction',        'ContaController', 'logoutContaAction');
        $this->addRoute('POST', '/conta/loginAction',        'ContaController', 'loginContaAction');
        $this->addRoute('POST', '/conta/alternarContaAction','ContaController', 'alternarContaAction');
        $this->addRoute('GET', '/conta/alternarContaAction', 'ContaController', 'alternarContaAction');
        $this->addRoute('POST', '/conta/criarContaAction',   'ContaController', 'criarContaAction');
        $this->addRoute('GET', '/conta/deposito',            'ContaController', 'depositoContaAction');
        $this->addRoute('POST', '/conta/deposito',           'ContaController', 'depositoContaAction');
        $this->addRoute('GET', '/conta/saque',               'ContaController', 'saqueContaAction');
        $this->addRoute('POST', '/conta/saque',              'ContaController', 'saqueContaAction');

        // Rotas para CaixaEletronicoController
        $this->addRoute('GET', '/caixa/estoqueCaixaAction',    'CaixaEletronicoController', 'estoqueCaixaAction');
        $this->addRoute('POST', '/caixa/estoqueCaixaAction',   'CaixaEletronicoController', 'estoqueCaixaAction');
        $this->addRoute('GET', 'caixa/carregar',             'CaixaEletronicoController', 'carregarCaixaEletronicoAction');
        $this->addRoute('POST', 'caixa/carregar',            'CaixaEletronicoController', 'carregarCaixaEletronicoAction');
        $this->addRoute('GET', 'caixa/descarregar',          'CaixaEletronicoController', 'descarregarCaixaEletronicoAction');
        $this->addRoute('POST', 'caixa/descarregar',         'CaixaEletronicoController', 'descarregarCaixaEletronicoAction');
    }
}