<?php

declare(strict_types=1);

/**
 * Simple front-controller router.
 * Maps URI paths to controller actions.
 */
class Router {

    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string $controller, string $action): void {
        $this->routes['GET'][$path] = [$controller, $action];
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $controller, string $action): void {
        $this->routes['POST'][$path] = [$controller, $action];
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $this->parseUri();

        // Try exact match first
        if (isset($this->routes[$method][$uri])) {
            [$controller, $action] = $this->routes[$method][$uri];
            $this->callAction($controller, $action, []);
            return;
        }

        // Try pattern matching for dynamic segments (:param)
        foreach ($this->routes[$method] ?? [] as $pattern => [$controller, $action]) {
            $params = $this->matchRoute($pattern, $uri);
            if ($params !== null) {
                $this->callAction($controller, $action, $params);
                return;
            }
        }

        // No match — 404
        $this->send404();
    }

    /**
     * Parse the request URI, stripping the base path and query string.
     */
    private function parseUri(): string {
        $uri      = $_SERVER['REQUEST_URI'] ?? '/';
        $basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';

        // Strip base path
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        // Strip query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    /**
     * Match a route pattern against a URI. Returns params array or null.
     * Pattern: /product/:slug  matches  /product/my-tee  → ['slug' => 'my-tee']
     */
    private function matchRoute(string $pattern, string $uri): ?array {
        // Convert :param to named capture groups
        $regex = preg_replace('/:([a-zA-Z_]+)/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        // Extract only named captures
        return array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Instantiate the controller and call the action method.
     */
    private function callAction(string $controller, string $action, array $params): void {
        $file = APP_PATH . '/Controllers/' . $controller . '.php';

        if (!file_exists($file)) {
            error_log("Controller not found: $controller");
            $this->send404();
            return;
        }

        require_once $file;

        if (!class_exists($controller)) {
            error_log("Controller class not found: $controller");
            $this->send404();
            return;
        }

        $instance = new $controller();

        if (!method_exists($instance, $action)) {
            error_log("Action not found: $controller::$action");
            $this->send404();
            return;
        }

        $instance->$action($params);
    }

    /**
     * Show the 404 page.
     */
    private function send404(): void {
        http_response_code(404);
        $file = VIEW_PATH . '/errors/404.php';
        if (file_exists($file)) {
            require $file;
        } else {
            echo '<!DOCTYPE html><html><body><h1>404 — Page Not Found</h1></body></html>';
        }
        exit;
    }
}
