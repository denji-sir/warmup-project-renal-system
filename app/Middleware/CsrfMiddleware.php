<?php

namespace App\Middleware;

use Core\CSRF;

/**
 * Middleware for CSRF protection on POST/PUT/PATCH/DELETE requests
 */
class CsrfMiddleware
{
    public function handle($request, $next)
    {
        $method = $request->getMethod();
        
        // Only check CSRF for state-changing methods
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!CSRF::verify($token)) {
                // Invalid CSRF token
                http_response_code(403);
                echo json_encode(['error' => 'CSRF token mismatch']);
                exit;
            }
        }

        return $next($request);
    }
}