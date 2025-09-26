<?php

namespace Core;

/**
 * HTTP Request wrapper
 */
class Request
{
    private array $query;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;
    
    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
        
        // Handle JSON input
        if ($this->isJson()) {
            $jsonInput = json_decode(file_get_contents('php://input'), true);
            if ($jsonInput) {
                $this->body = array_merge($this->body, $jsonInput);
            }
        }
        
        // Handle method spoofing
        if ($this->has('_method')) {
            $this->server['REQUEST_METHOD'] = strtoupper($this->input('_method'));
        }
    }
    
    /**
     * Get request method
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }
    
    /**
     * Check if request method matches
     */
    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }
    
    /**
     * Get request URI
     */
    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return $uri;
    }
    
    /**
     * Get full URL
     */
    public function url(): string
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}{$this->uri()}";
    }
    
    /**
     * Check if request is secure (HTTPS)
     */
    public function isSecure(): bool
    {
        return ($this->server['HTTPS'] ?? '') === 'on' ||
               ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }
    
    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return strtolower($this->header('X-Requested-With') ?? '') === 'xmlhttprequest';
    }
    
    /**
     * Check if request expects JSON
     */
    public function expectsJson(): bool
    {
        return $this->isAjax() || 
               str_contains($this->header('Accept') ?? '', 'application/json');
    }
    
    /**
     * Check if request content is JSON
     */
    public function isJson(): bool
    {
        return str_contains($this->header('Content-Type') ?? '', 'application/json');
    }
    
    /**
     * Get input value from query or body
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
    
    /**
     * Get all input data
     */
    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }
    
    /**
     * Check if input key exists
     */
    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }
    
    /**
     * Get only specified input keys
     */
    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }
        return $result;
    }
    
    /**
     * Get all input except specified keys
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }
    
    /**
     * Get query parameter
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }
    
    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }
    
    /**
     * Check if file was uploaded
     */
    public function hasFile(string $key): bool
    {
        $file = $this->file($key);
        return $file && $file['error'] === UPLOAD_ERR_OK;
    }
    
    /**
     * Get header value
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[strtolower($key)] ?? $default;
    }
    
    /**
     * Get all headers
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    /**
     * Get user IP address
     */
    public function ip(): string
    {
        // Check for IP from various proxy headers
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ipKeys as $key) {
            if (!empty($this->server[$key])) {
                $ips = explode(',', $this->server[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Get user agent
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
    
    /**
     * Parse headers from server variables
     */
    private function parseHeaders(): array
    {
        $headers = [];
        
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }
    
    /**
     * Validate CSRF token
     */
    public function validateCsrfToken(): bool
    {
        $token = $this->input('_token');
        return $token && CSRF::verify($token);
    }
}