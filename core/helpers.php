<?php

/**
 * Core Helper Functions
 * Global utility functions available throughout the application
 */

if (!function_exists('env')) {
    /**
     * Get environment variable with optional default
     */
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value using dot notation
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $config = null;
        
        if ($config === null) {
            $config = require __DIR__ . '/../config/config.php';
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL for given path
     */
    function url(string $path = ''): string
    {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/');
        $path = ltrim($path, '/');
        return $path ? "{$baseUrl}/{$path}" : $baseUrl;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset(string $path): string
    {
        return url("assets/{$path}");
    }
}

if (!function_exists('upload_url')) {
    /**
     * Generate upload URL
     */
    function upload_url(string $path): string
    {
        return url("uploads/{$path}");
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL
     */
    function redirect(string $url, int $code = 302): never
    {
        header("Location: {$url}", true, $code);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back with fallback
     */
    function back(string $fallback = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        $url = $referer && parse_url($referer, PHP_URL_HOST) === $_SERVER['HTTP_HOST'] 
            ? $referer 
            : url($fallback);
        redirect($url);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value from session
     */
    function old(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get CSRF token
     */
    function csrf_token(): string
    {
        return \Core\CSRF::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF hidden input field
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return "<input type=\"hidden\" name=\"_token\" value=\"{$token}\">";
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate method spoofing field for forms
     */
    function method_field(string $method): string
    {
        return "<input type=\"hidden\" name=\"_method\" value=\"{$method}\">";
    }
}

if (!function_exists('auth')) {
    /**
     * Get authenticated user or auth instance
     */
    function auth(): \Core\Auth
    {
        static $auth = null;
        if ($auth === null) {
            $auth = new \Core\Auth();
        }
        return $auth;
    }
}

if (!function_exists('user')) {
    /**
     * Get authenticated user
     */
    function user(): ?\App\Models\User
    {
        $userData = auth()->user();
        
        if (!$userData) {
            return null;
        }
        
        // Создаем объект User из массива данных
        $user = new \App\Models\User();
        foreach ($userData as $key => $value) {
            $user->$key = $value;
        }
        
        return $user;
    }
}

if (!function_exists('session')) {
    /**
     * Get session instance or session value
     */
    function session(?string $key = null, mixed $default = null): mixed
    {
        static $session = null;
        if ($session === null) {
            $session = new \Core\Session();
        }
        
        if ($key === null) {
            return $session;
        }
        
        return $session->get($key, $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash message
     */
    function flash(string $type, string $message): void
    {
        session()->flash($type, $message);
    }
}

if (!function_exists('logger')) {
    /**
     * Get logger instance or log message
     */
    function logger(?string $message = null, string $level = 'info'): \Core\Logger
    {
        static $logger = null;
        if ($logger === null) {
            $logger = new \Core\Logger();
        }
        
        if ($message !== null) {
            $logger->log($level, $message);
        }
        
        return $logger;
    }
}

if (!function_exists('__')) {
    /**
     * Translate string (basic i18n)
     */
    function __(string $key, array $replace = []): string
    {
        static $translations = null;
        
        if ($translations === null) {
            $locale = config('app.locale', 'ru');
            $file = __DIR__ . "/../resources/lang/{$locale}.php";
            $translations = file_exists($file) ? require $file : [];
        }
        
        $translation = $translations[$key] ?? $key;
        
        foreach ($replace as $search => $replacement) {
            $translation = str_replace(":{$search}", $replacement, $translation);
        }
        
        return $translation;
    }
}

if (!function_exists('json_response')) {
    /**
     * Return JSON response
     */
    function json_response(mixed $data, int $status = 200): never
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with HTTP status code
     */
    function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        
        if ($message) {
            echo $message;
        } else {
            // Load error page if exists
            $errorFile = __DIR__ . "/../app/Views/errors/{$code}.php";
            if (file_exists($errorFile)) {
                require $errorFile;
            } else {
                echo "Error {$code}";
            }
        }
        
        exit;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die (for debugging)
     */
    function dd(...$vars): never
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        exit;
    }
}

if (!function_exists('now')) {
    /**
     * Get current datetime
     */
    function now(): DateTime
    {
        return new DateTime();
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate URL-friendly slug
     */
    function str_slug(string $string, string $separator = '-'): string
    {
        // Convert to lowercase
        $string = mb_strtolower($string);
        
        // Transliterate Cyrillic to Latin
        $transliteration = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya', 'ь' => '', 'ъ' => ''
        ];
        
        $string = strtr($string, $transliteration);
        
        // Remove special characters
        $string = preg_replace('/[^a-z0-9\-_\s]/', '', $string);
        
        // Replace spaces and multiple separators with single separator
        $string = preg_replace('/[\s\-_]+/', $separator, $string);
        
        // Trim separators from ends
        return trim($string, $separator);
    }
}