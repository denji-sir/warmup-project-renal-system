<?php

/**
 * Authentication helper functions
 */

if (!function_exists('auth')) {
    /**
     * Get the current authenticated user
     */
    function auth()
    {
        $auth = new \Core\Auth();
        return $auth->user();
    }
}

if (!function_exists('user')) {
    /**
     * Get the current authenticated user (alias for auth())
     */
    function user()
    {
        return auth();
    }
}

if (!function_exists('isLoggedIn')) {
    /**
     * Check if user is authenticated
     */
    function isLoggedIn(): bool
    {
        $auth = new \Core\Auth();
        return $auth->check();
    }
}

if (!function_exists('isGuest')) {
    /**
     * Check if user is not authenticated
     */
    function isGuest(): bool
    {
        return !isLoggedIn();
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if user has specific role
     */
    function hasRole(string $role): bool
    {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        $auth = new \Core\Auth();
        return $auth->hasRole($role);
    }
}

if (!function_exists('can')) {
    /**
     * Check if user has specific permission
     */
    function can(string $permission): bool
    {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        $auth = new \Core\Auth();
        return $auth->can($permission);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL
     */
    function redirect(string $url = '/')
    {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to previous page
     */
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('intended')) {
    /**
     * Redirect to intended URL or default
     */
    function intended(string $default = '/')
    {
        $session = new \Core\Session();
        $intended = $session->get('intended_url', $default);
        $session->forget('intended_url');
        redirect($intended);
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
     * Get CSRF hidden input field
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return "<input type=\"hidden\" name=\"_token\" value=\"$token\">";
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     */
    function old(string $key, $default = '')
    {
        $session = new \Core\Session();
        $oldInput = $session->get('old_input', []);
        return $oldInput[$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    /**
     * Get validation errors
     */
    function errors(string $key = null)
    {
        $session = new \Core\Session();
        $errors = $session->get('errors', []);
        
        if ($key) {
            return $errors[$key] ?? [];
        }
        
        return $errors;
    }
}

if (!function_exists('hasError')) {
    /**
     * Check if field has validation error
     */
    function hasError(string $key): bool
    {
        $fieldErrors = errors($key);
        return !empty($fieldErrors);
    }
}

if (!function_exists('firstError')) {
    /**
     * Get first validation error for field
     */
    function firstError(string $key): string
    {
        $fieldErrors = errors($key);
        return $fieldErrors[0] ?? '';
    }
}

if (!function_exists('flash')) {
    /**
     * Get flash message
     */
    function flash(string $key = null)
    {
        $session = new \Core\Session();
        
        if ($key) {
            return $session->getFlash($key);
        }
        
        return [
            'success' => $session->getFlash('success'),
            'error' => $session->getFlash('error'),
            'warning' => $session->getFlash('warning'),
            'info' => $session->getFlash('info')
        ];
    }
}

if (!function_exists('hasFlash')) {
    /**
     * Check if flash message exists
     */
    function hasFlash(string $key): bool
    {
        $message = flash($key);
        return !empty($message);
    }
}