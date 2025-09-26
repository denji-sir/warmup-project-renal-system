<?php

/**
 * Helper functions for easy access to configuration and environment variables
 */

if (!function_exists('env')) {
    /**
     * Get environment variable with fallback
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key) ?: $default;
        
        // Convert string values to appropriate types
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
            }
            
            // Handle quoted strings
            if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                return $matches[1];
            }
        }
        
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value using dot notation
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        static $config = null;
        
        if ($config === null) {
            $config = require __DIR__ . '/config.php';
        }
        
        $segments = explode('.', $key);
        $value = $config;
        
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
}

if (!function_exists('app_path')) {
    /**
     * Get path relative to application root
     * 
     * @param string $path
     * @return string
     */
    function app_path(string $path = ''): string
    {
        return __DIR__ . '/../' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('public_path')) {
    /**
     * Get path to public directory
     * 
     * @param string $path
     * @return string
     */
    function public_path(string $path = ''): string
    {
        return app_path('public') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get path to storage directory
     * 
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return app_path('storage') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('resource_path')) {
    /**
     * Get path to resources directory
     * 
     * @param string $path
     * @return string
     */
    function resource_path(string $path = ''): string
    {
        return app_path('resources') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL for the given path
     * 
     * @param string $path
     * @param array $parameters
     * @return string
     */
    function url(string $path = '', array $parameters = []): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $path = '/' . ltrim($path, '/');
        
        if (!empty($parameters)) {
            $path .= '?' . http_build_query($parameters);
        }
        
        return $baseUrl . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for asset
     * 
     * @param string $path
     * @return string
     */
    function asset(string $path): string
    {
        $version = config('performance.assets_version', '1.0.0');
        $cdnUrl = config('performance.cdn_url');
        
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/') . '?v=' . $version;
        }
        
        return url($path) . '?v=' . $version;
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response
     * 
     * @param string $path
     * @param int $status
     * @return void
     */
    function redirect(string $path, int $status = 302): void
    {
        header('Location: ' . url($path), true, $status);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to previous page
     * 
     * @param string $fallback
     * @return void
     */
    function back(string $fallback = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        $url = $referer ?: url($fallback);
        
        header('Location: ' . $url, true, 302);
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Abort request with HTTP status code
     * 
     * @param int $code
     * @param string $message
     * @return void
     */
    function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        
        if ($message) {
            echo $message;
        }
        
        exit;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die (for debugging)
     * 
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variable (for debugging)
     * 
     * @param mixed $var
     * @return mixed
     */
    function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        
        return $var;
    }
}