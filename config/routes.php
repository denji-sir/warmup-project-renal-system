<?php

use Core\Router;

/** @var Router $router */
if (!isset($router) || !$router instanceof Router) {
    $router = new Router();
}

// Home routes
$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/services', 'HomeController@services');
$router->get('/contact', 'HomeController@contact');
$router->post('/contact', 'HomeController@contact');
$router->get('/privacy', 'HomeController@privacy');
$router->get('/terms', 'HomeController@terms');

// Authentication routes
$router->get('/auth/login', 'AuthController@loginForm');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/register', 'AuthController@registerForm');
$router->post('/auth/register', 'AuthController@register');
$router->post('/auth/logout', 'AuthController@logout');

// Password reset routes
$router->get('/auth/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/auth/forgot-password', 'AuthController@forgotPassword');
$router->get('/auth/reset-password/{token}', 'AuthController@resetPasswordForm');
$router->post('/auth/reset-password', 'AuthController@resetPassword');

// Email verification routes
$router->get('/auth/verify-email/{token}', 'AuthController@verifyEmail');
$router->post('/auth/resend-verification', 'AuthController@resendVerification');

// API routes
$router->get('/api/search', 'HomeController@search');

// SEO routes
$router->get('/sitemap.xml', 'HomeController@sitemap');
