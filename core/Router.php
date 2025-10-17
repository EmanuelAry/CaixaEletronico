<?php
namespace core;
class Router {
    protected $routes = [];

    public function add($route, $params) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'] . 'Controller';
            $action = $this->routes[$url]['action'];

            $controllerFile = "../src/Controller/{$controller}.php";
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerObject = new $controller();
                $controllerObject->$action();
            } else {
                throw new \Exception("Controller file not found");
            }
        } else {
            throw new \Exception("No route matched", 404);
        }
    }
}