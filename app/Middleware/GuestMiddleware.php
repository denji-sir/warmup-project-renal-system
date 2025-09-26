<?php

namespace App\Middleware;

use Core\Auth;

/**
 * Middleware for checking if user is not authenticated (guest)
 */
class GuestMiddleware
{
    public function handle($request, $next)
    {
        $auth = new Auth();
        
        if ($auth->check()) {
            // User is logged in, redirect to home
            header('Location: /');
            exit;
        }

        return $next($request);
    }
}