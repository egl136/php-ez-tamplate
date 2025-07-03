<?php
namespace App\Core\Router;
use App\Core\Classes\Request;
require_once __DIR__ . '/../Classes/Request.php';
class Router {
    protected $routes = [];

    public function get(string $route, string $action) {
        $this->addRoute('GET', $route, $action);
    }

    public function post(string $route, string $action) {
        $this->addRoute('POST', $route, $action);
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

        // Instantiate the Request object once for the current request
        $request = new Request();

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Remove the full match
                $routeParams = array_combine($route['params'], $matches);

                [$controllerName, $methodName] = explode('@', $route['action']);
                $controller = "App\\Core\\Controllers\\$controllerName";
                $path = __DIR__ . "/../../Core/Controllers/$controllerName.php";
                $pathToController = realpath($path);

                if (!file_exists($pathToController)) {
                    http_response_code(500);
                    echo "Controller file not found: " . $pathToController;
                    return;
                }
                require_once $pathToController;

                // Create an instance of the controller
                $controllerInstance = new $controller();

                // Determine the parameters to pass to the controller method
                $reflectionMethod = new \ReflectionMethod($controllerInstance, $methodName);
                $methodParameters = $reflectionMethod->getParameters();

                $args = [];
                foreach ($methodParameters as $param) {
                    $paramName = $param->getName();
                    $paramType = $param->getType();

                    if ($paramType && $paramType->getName() === Request::class) {
                        // If the parameter is type-hinted as Request, pass the request object
                        $args[] = $request;
                    } elseif (isset($routeParams[$paramName])) {
                        // If the parameter name matches a route parameter, pass it
                        $args[] = $routeParams[$paramName];
                    } elseif ($param->isDefaultValueAvailable()) {
                        // Use default value if available and no other match
                        $args[] = $param->getDefaultValue();
                    } else {
                        // Handle missing required parameters (e.g., throw an exception)
                        // For now, let's just add null, but in a real app you might want to error
                        $args[] = null;
                    }
                }

                // Call the controller method with the prepared arguments
                call_user_func_array([$controllerInstance, $methodName], $args);
                return;
            }
        }

        http_response_code(404); // Set 404 status code
        echo "404 Not Found";
    }
    /*public function dispatch(string $uri, string $method = 'GET') {
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
    }*/
}
