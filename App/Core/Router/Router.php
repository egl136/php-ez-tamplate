<?php
namespace App\Core\Router;

class Router {
    protected $routes = [];

    public function get(string $route, string $action) {
        $this->addRoute('GET', $route, $action);
    }

    public function put(string $route, string $action) {
        $this->addRoute('PUT', $route, $action);
    }

    public function delete(string $route, string $action) {
        $this->addRoute('DELETE', $route, $action);
        
    }

    public function patch(string $route, string $action) {
        $this->addRoute('PATCH', $route, $action);
    }

    protected function addRoute(string $method, string $route, string $action) {
        $route = rtrim($route, '/') ?: '/';

        // Convert /article/{id} to a regex pattern
        $pattern = preg_replace('#\{([^}]+)\}#', '([^/]+)', $route);
        $pattern = "#^$pattern$#";

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'action' => $action,
            'params' => $this->extractParamNames($route)
        ];
    }

    protected function extractParamNames(string $route): array {
        preg_match_all('#\{([^}]+)\}#', $route, $matches);
        return $matches[1]; // returns something like ['id']
    }

    public function dispatch(string $uri, string $method = 'GET') {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        if (!isset($this->routes[$method])) {
            http_response_code(405);
            echo "405 Method Not Allowed";
            return;
        }

        foreach ($this->routes[$method] as $route) {
           
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Remove the full match
                $params = array_combine($route['params'], $matches);
                [$controllerName, $methodName] = explode('@', $route['action']);
                $controller = "App\\Core\\Controllers\\$controllerName";
                $path = __DIR__ . "/../../Core/Controllers/$controllerName.php";
                $pathToController = realpath($path);
                require_once $pathToController;
                call_user_func_array([new $controller, $methodName], $params);
                return;
            }
        }

        //http_response_code(404);
        echo "404 Not Found";
    }
}
