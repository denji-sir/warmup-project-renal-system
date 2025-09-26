<?php

namespace Core;

/**
 * URL Router with middleware support
 */
class Router
{
    private array $routes = [];
    private array $middleware = [];
    private array $groups = [];
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddleware = [];
    
    /**
     * Add GET route
     */
    public function get(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }
    
    /**
     * Add POST route
     */
    public function post(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }
    
    /**
     * Add PUT route
     */
    public function put(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $pattern, $handler, $middleware);
    }
    
    /**
     * Add PATCH route
     */
    public function patch(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('PATCH', $pattern, $handler, $middleware);
    }
    
    /**
     * Add DELETE route
     */
    public function delete(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $pattern, $handler, $middleware);
    }
    
    /**
     * Add route for multiple methods
     */
    public function match(array $methods, string $pattern, callable|string $handler, array $middleware = []): void
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $pattern, $handler, $middleware);
        }
    }
    
    /**
     * Add route for all HTTP methods
     */
    public function any(string $pattern, callable|string $handler, array $middleware = []): void
    {
        $this->match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $pattern, $handler, $middleware);
    }
    
    /**
     * Group routes with common attributes
     */
    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddleware = $this->currentGroupMiddleware;
        
        // Set group attributes
        if (isset($attributes['prefix'])) {
            $this->currentGroupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }
        
        if (isset($attributes['middleware'])) {
            $this->currentGroupMiddleware = array_merge(
                $previousMiddleware,
                (array) $attributes['middleware']
            );
        }
        
        // Execute callback to register routes
        $callback($this);
        
        // Restore previous settings
        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddleware = $previousMiddleware;
    }
    
    /**
     * Add middleware
     */
    public function middleware(string $name, callable $handler): void
    {
        $this->middleware[$name] = $handler;
    }
    
    /**
     * Dispatch request
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $this->cleanUri($request->uri());
        
        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPattern($route['pattern'], $uri)) {
                $params = $this->extractParams($route['pattern'], $uri);
                
                // Execute middleware chain
                return $this->executeMiddleware($route, $request, $params);
            }
        }
        
        // No route found
        return $this->handleNotFound($request);
    }
    
    /**
     * Add route to collection
     */
    private function addRoute(string $method, string $pattern, callable|string $handler, array $middleware = []): void
    {
        $fullPattern = $this->currentGroupPrefix . $pattern;
        $allMiddleware = array_merge($this->currentGroupMiddleware, $middleware);
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->cleanPattern($fullPattern),
            'handler' => $handler,
            'middleware' => $allMiddleware
        ];
    }
    
    /**
     * Clean URI pattern
     */
    private function cleanPattern(string $pattern): string
    {
        return '/' . trim($pattern, '/');
    }
    
    /**
     * Clean request URI
     */
    private function cleanUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        return '/' . trim($uri, '/');
    }
    
    /**
     * Check if pattern matches URI
     */
    private function matchPattern(string $pattern, string $uri): bool
    {
        // Convert pattern to regex
        $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $regex = str_replace('/', '\/', $regex);
        $regex = '/^' . $regex . '$/';
        
        return preg_match($regex, $uri);
    }
    
    /**
     * Extract parameters from URI
     */
    private function extractParams(string $pattern, string $uri): array
    {
        $params = [];
        
        // Get parameter names from pattern
        preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
        
        // Get parameter values from URI
        $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $regex = str_replace('/', '\/', $regex);
        $regex = '/^' . $regex . '$/';
        
        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches); // Remove full match
            
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }
        }
        
        return $params;
    }
    
    /**
     * Execute middleware chain and handler
     */
    private function executeMiddleware(array $route, Request $request, array $params): Response
    {
        $middlewareChain = $route['middleware'];
        
        // Create final handler
        $finalHandler = function () use ($route, $request, $params) {
            return $this->callHandler($route['handler'], $request, $params);
        };
        
        // Build middleware chain (reverse order)
        $handler = array_reduce(
            array_reverse($middlewareChain),
            function ($next, $middlewareName) use ($request) {
                return function () use ($middlewareName, $next, $request) {
                    if (isset($this->middleware[$middlewareName])) {
                        return $this->middleware[$middlewareName]($request, $next);
                    }
                    return $next();
                };
            },
            $finalHandler
        );
        
        return $handler();
    }
    
    /**
     * Call route handler
     */
    private function callHandler(callable|string $handler, Request $request, array $params): Response
    {
        // If handler is string, assume it's Controller@method
        if (is_string($handler)) {
            if (strpos($handler, '@') === false) {
                throw new \Exception("Invalid handler format: {$handler}");
            }

            [$controllerClass, $method] = explode('@', $handler, 2);
            $controllerClass = $this->resolveControllerClass($controllerClass);

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass();

            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($request);
            }

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }

            $result = $controller->$method(...array_values($params));
        } else {
            // Callable handler
            $result = $handler($request, ...array_values($params));
        }

        // Convert result to Response object
        if ($result instanceof Response) {
            return $result;
        } elseif (is_string($result)) {
            return Response::html($result);
        } elseif (is_array($result) || is_object($result)) {
            return Response::json($result);
        } else {
            return Response::html((string) $result);
        }
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(Request $request): Response
    {
        if ($request->expectsJson()) {
            return Response::json(['error' => 'Route not found'], 404);
        }
        
        // Try to load 404 view
        try {
            $view = new View();
            $content = $view->render('errors.404');
            return Response::html($content, 404);
        } catch (\Exception $e) {
            return Response::html('404 - Page Not Found', 404);
        }
    }
    
    /**
     * Load routes from file
     */
    public function loadFromFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Routes file not found: {$filePath}");
        }
        
        require $filePath;
    }
    
    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Resolve controller class from short or fully-qualified notation
     */
    private function resolveControllerClass(string $controller): string
    {
        $controller = ltrim($controller, '\\');

        if (str_contains($controller, '\\')) {
            return $controller;
        }

        return 'App\\Controllers\\' . $controller;
    }
}
