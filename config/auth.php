<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    */

    // Session timeout in minutes (0 = until browser closes)
    'session_timeout' => 120,

    // Remember me timeout in days
    'remember_me_timeout' => 30,

    // Password reset token expiration in minutes
    'password_reset_timeout' => 60,

    // Email verification token expiration in minutes
    'email_verification_timeout' => 1440, // 24 hours

    // Maximum login attempts before lockout
    'max_login_attempts' => 5,

    // Account lockout duration in minutes
    'lockout_duration' => 15,

    // Password requirements
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
    ],

    // User roles and permissions
    'roles' => [
        'admin' => [
            'name' => 'Администратор',
            'permissions' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'properties.view',
                'properties.create',
                'properties.edit',
                'properties.delete',
                'rentals.view',
                'rentals.create',
                'rentals.edit',
                'rentals.delete',
                'payments.view',
                'payments.create',
                'payments.edit',
                'payments.delete',
                'reports.view',
                'settings.view',
                'settings.edit',
            ],
        ],
        'manager' => [
            'name' => 'Менеджер',
            'permissions' => [
                'properties.view',
                'properties.create',
                'properties.edit',
                'rentals.view',
                'rentals.create',
                'rentals.edit',
                'payments.view',
                'payments.create',
                'reports.view',
            ],
        ],
        'tenant' => [
            'name' => 'Арендатор',
            'permissions' => [
                'rentals.view_own',
                'payments.view_own',
                'properties.view_available',
            ],
        ],
        'owner' => [
            'name' => 'Собственник',
            'permissions' => [
                'properties.view_own',
                'properties.create',
                'properties.edit_own',
                'rentals.view_own_properties',
                'payments.view_own_properties',
                'reports.view_own',
            ],
        ],
    ],

    // Default role for new users
    'default_role' => 'tenant',

    // Require email verification for new accounts
    'require_email_verification' => true,

    // Allow registration (can be disabled in production)
    'allow_registration' => true,

    // Login with username or email
    'login_fields' => ['username', 'email'], // or just ['email'] for email only

    // Redirect paths
    'redirects' => [
        'after_login' => '/',
        'after_logout' => '/login',
        'after_registration' => '/verify-email',
        'after_password_reset' => '/login',
        'after_email_verification' => '/',
    ],

    // Security settings
    'security' => [
        // Enable CSRF protection
        'csrf_protection' => true,
        
        // Secure cookies (HTTPS only)
        'secure_cookies' => false, // Set to true in production with HTTPS
        
        // HTTP only cookies
        'http_only_cookies' => true,
        
        // Same site cookie setting
        'same_site' => 'Lax', // None, Lax, Strict
        
        // IP validation for sessions
        'validate_ip' => false,
        
        // User agent validation for sessions
        'validate_user_agent' => true,
        
        // Session regeneration on login
        'regenerate_on_login' => true,
    ],

    // Email settings for authentication emails
    'email' => [
        'from_address' => 'noreply@rental-system.local',
        'from_name' => 'Система Аренды',
        'verification_subject' => 'Подтвердите ваш email',
        'password_reset_subject' => 'Сброс пароля',
    ],
];