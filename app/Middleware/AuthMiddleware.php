<?php

namespace App\Middleware;

use Core\Auth;
use Core\Session;

/**
 * Middleware for checking if user is authenticated
 */
class AuthMiddleware
{
    public function handle($request, $next)
    {
        $auth = new Auth();
        $session = new Session();
        
        if (!$auth->check()) {
            // Store intended URL for redirect after login
            $intendedUrl = $request->getUri()->getPath();
            if ($intendedUrl !== '/login' && $intendedUrl !== '/register') {
                $session->set('intended_url', $intendedUrl);
            }
            
            // Redirect to login page
            header('Location: /login');
            exit;
        }

        return $next($request);
    }
}