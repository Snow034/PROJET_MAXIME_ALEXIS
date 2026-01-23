<?php
namespace App\Core;
class Router
{
    private array $routes = [];
    public function get(string $path, array $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }
public function post(string $path, array $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }
    private function addRoute(string $method, string $path, array $handler)
    {
        $this->routes[$method][$path] = $handler;
    }
    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
$uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            $controllerClass = $handler[0];
            $action = $handler[1];
            try {
if (!class_exists($controllerClass)) {
                    throw new \Exception("Controller class $controllerClass not found");
                }
                $controller = new $controllerClass();
if (!method_exists($controller, $action)) {
                    throw new \Exception("Method $action not found in $controllerClass");
                }
                $controller->$action();
            } catch (\Throwable $e) {
                echo "<div style='color:red; background:white; padding:10px; border:1px solid red;'>";
                echo "<strong>CRASH ROUTER:</strong> " . $e->getMessage() . "<br>";
                echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                echo "</div>";
                exit;
            }
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}