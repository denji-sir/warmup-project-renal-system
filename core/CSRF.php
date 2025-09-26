<?php

namespace Core;

/**
 * CSRF Protection
 */
class CSRF
{
    private const TOKEN_LENGTH = 32;
    private const TOKEN_KEY = '_csrf_token';
    
    /**
     * Generate CSRF token
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }
    
    /**
     * Get current CSRF token (generate if doesn't exist)
     */
    public static function token(): string
    {
        $session = new Session();
        
        if (!$session->has(self::TOKEN_KEY)) {
            $session->set(self::TOKEN_KEY, self::generateToken());
        }
        
        return $session->get(self::TOKEN_KEY);
    }
    
    /**
     * Verify CSRF token
     */
    public static function verify(string $token): bool
    {
        $session = new Session();
        $sessionToken = $session->get(self::TOKEN_KEY);
        
        if (!$sessionToken || !$token) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Generate new token (invalidate current)
     */
    public static function regenerateToken(): string
    {
        $session = new Session();
        $newToken = self::generateToken();
        $session->set(self::TOKEN_KEY, $newToken);
        return $newToken;
    }
    
    /**
     * Create CSRF hidden input field
     */
    public static function field(): string
    {
        $token = self::token();
        return "<input type=\"hidden\" name=\"_token\" value=\"{$token}\">";
    }
    
    /**
     * Create CSRF meta tag for AJAX requests
     */
    public static function metaTag(): string
    {
        $token = self::token();
        return "<meta name=\"csrf-token\" content=\"{$token}\">";
    }
    
    /**
     * Validate request CSRF token
     */
    public static function validateRequest(Request $request): bool
    {
        // Skip CSRF for GET, HEAD, OPTIONS requests
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }
        
        $token = $request->input('_token') ?: $request->header('X-CSRF-Token');
        
        if (!$token) {
            return false;
        }
        
        return self::verify($token);
    }
    
    /**
     * Middleware to check CSRF token
     */
    public static function middleware(): callable
    {
        return function (Request $request, callable $next) {
            if (!self::validateRequest($request)) {
                if ($request->expectsJson()) {
                    return Response::json([
                        'error' => 'CSRF token mismatch',
                        'message' => 'The request could not be completed due to invalid CSRF token.'
                    ], 419);
                }
                
                // Store flash message and redirect back
                $session = new Session();
                $session->flash('error', 'Security token mismatch. Please try again.');
                
                return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            
            return $next($request);
        };
    }
}