<?php

namespace Core;

/**
 * Session management with security features
 */
class Session
{
    private bool $started = false;
    
    public function __construct()
    {
        $this->configureSession();
        $this->start();
    }
    
    /**
     * Configure session settings
     */
    private function configureSession(): void
    {
        // Only configure if session is not active
        if (session_status() === PHP_SESSION_NONE) {
            // Session configuration
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', env('SESSION_SAMESITE', 'Lax'));
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            
            // Set secure flag for HTTPS
            if (env('APP_ENV') === 'production') {
                ini_set('session.cookie_secure', '1');
            }
            
            // Session name
            session_name(env('SESSION_NAME', 'RESESSID'));
            
            // Session lifetime
            $lifetime = (int) env('SESSION_LIFETIME', 3600);
            ini_set('session.gc_maxlifetime', (string) $lifetime);
            session_set_cookie_params($lifetime);
        }
    }
    
    /**
     * Start session if not already started
     */
    public function start(): void
    {
        if (!$this->started && session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
            
            // Regenerate session ID periodically for security
            $this->regenerateIfNeeded();
        }
    }
    
    /**
     * Regenerate session ID if needed
     */
    private function regenerateIfNeeded(): void
    {
        $regenerateInterval = 30 * 60; // 30 minutes
        
        if (!isset($_SESSION['_session_started'])) {
            $_SESSION['_session_started'] = time();
        } elseif (time() - $_SESSION['_session_started'] > $regenerateInterval) {
            $this->regenerate();
            $_SESSION['_session_started'] = time();
        }
    }
    
    /**
     * Regenerate session ID
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        if ($this->started) {
            session_regenerate_id($deleteOldSession);
        }
    }
    
    /**
     * Get session value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Set session value
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Check if session key exists
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session key
     */
    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Clear all session data
     */
    public function clear(): void
    {
        $_SESSION = [];
    }
    
    /**
     * Destroy session
     */
    public function destroy(): void
    {
        if ($this->started) {
            $_SESSION = [];
            
            // Delete the session cookie
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
            
            session_destroy();
            $this->started = false;
        }
    }
    
    /**
     * Flash message - store for next request only
     */
    public function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }
    
    /**
     * Get flash messages
     */
    public function getFlash(?string $type = null): array
    {
        if ($type) {
            return $_SESSION['_flash'][$type] ?? [];
        }
        
        return $_SESSION['_flash'] ?? [];
    }
    
    /**
     * Check if has flash messages
     */
    public function hasFlash(?string $type = null): bool
    {
        if ($type) {
            return !empty($_SESSION['_flash'][$type]);
        }
        
        return !empty($_SESSION['_flash']);
    }
    
    /**
     * Clear flash messages (called after displaying)
     */
    public function clearFlash(): void
    {
        unset($_SESSION['_flash']);
    }
    
    /**
     * Store old input for form repopulation
     */
    public function flashInput(array $input): void
    {
        $_SESSION['_old'] = $input;
    }
    
    /**
     * Get old input value
     */
    public function getOld(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
    
    /**
     * Clear old input
     */
    public function clearOld(): void
    {
        unset($_SESSION['_old']);
    }
    
    /**
     * Get session ID
     */
    public function getId(): string
    {
        return session_id();
    }
    
    /**
     * Set session ID
     */
    public function setId(string $id): void
    {
        session_id($id);
    }
    
    /**
     * Get all session data
     */
    public function all(): array
    {
        return $_SESSION;
    }
    
    /**
     * Check if session is started
     */
    public function isStarted(): bool
    {
        return $this->started;
    }
    
    /**
     * Put value (alias for set)
     */
    public function put(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    /**
     * Pull value (get and forget)
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }
    
    /**
     * Increment session value
     */
    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }
    
    /**
     * Decrement session value
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }
}