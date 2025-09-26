<?php

/**
 * Application configuration
 */
return [
    'app' => [
        'name' => env('APP_NAME', 'Real Estate System'),
        'env' => env('APP_ENV', 'development'),
        'debug' => env('APP_DEBUG', true),
        'url' => env('APP_URL', 'http://localhost:8000'),
        'timezone' => env('TIMEZONE', 'Europe/Moscow'),
        'locale' => env('DEFAULT_LOCALE', 'ru'),
        'available_locales' => explode(',', env('AVAILABLE_LOCALES', 'ru,en')),
    ],
    
    'database' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 3306),
        'name' => env('DB_NAME', 'realestate'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_0900_ai_ci'),
    ],
    
    'session' => [
        'name' => env('SESSION_NAME', 'RESESSID'),
        'lifetime' => env('SESSION_LIFETIME', 3600),
        'httponly' => env('SESSION_HTTPONLY', true),
        'samesite' => env('SESSION_SAMESITE', 'Lax'),
        'secure' => env('SESSION_SECURE', false),
    ],
    
    'security' => [
        'csrf_secret' => env('CSRF_SECRET', 'change_me'),
        'jwt_secret' => env('JWT_SECRET', 'change_me'),
        'bcrypt_rounds' => env('BCRYPT_ROUNDS', 12),
    ],
    
    'uploads' => [
        'max_size_mb' => env('UPLOAD_MAX_MB', 8),
        'allowed_types' => explode(',', env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp')),
        'path' => env('UPLOAD_PATH', 'storage/uploads'),
    ],
    
    'exports' => [
        'max_rows' => env('EXPORT_MAX_ROWS', 20000),
        'path' => env('EXPORT_PATH', 'storage/exports'),
    ],
    
    'features' => [
        'dev_cart_enabled' => env('DEV_CART_ENABLED', true),
        'email_verification_enabled' => env('EMAIL_VERIFICATION_ENABLED', false),
        'registration_enabled' => env('REGISTRATION_ENABLED', true),
    ],
    
    'mail' => [
        'driver' => env('MAIL_DRIVER', 'smtp'),
        'host' => env('MAIL_HOST', 'localhost'),
        'port' => env('MAIL_PORT', 1025),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@realestate.local'),
        'from_name' => env('MAIL_FROM_NAME', 'Real Estate System'),
    ],
    
    'logging' => [
        'level' => env('LOG_LEVEL', 'debug'),
        'path' => env('LOG_PATH', 'storage/logs/app.log'),
        'error_path' => env('ERROR_LOG_PATH', 'storage/logs/error.log'),
        'audit_path' => env('AUDIT_LOG_PATH', 'storage/logs/audit.log'),
    ],
    
    'rate_limiting' => [
        'enabled' => env('RATE_LIMIT_ENABLED', true),
        'max_requests' => env('RATE_LIMIT_MAX_REQUESTS', 100),
        'window' => env('RATE_LIMIT_WINDOW', 3600),
    ],
    
    'search' => [
        'results_per_page' => env('SEARCH_RESULTS_PER_PAGE', 12),
        'max_radius_km' => env('SEARCH_MAX_RADIUS_KM', 50),
        'fulltext_min_length' => env('FULLTEXT_MIN_WORD_LENGTH', 3),
    ],
    
    'images' => [
        'resize_enabled' => env('IMAGE_RESIZE_ENABLED', true),
        'max_width' => env('IMAGE_MAX_WIDTH', 1920),
        'max_height' => env('IMAGE_MAX_HEIGHT', 1080),
        'quality' => env('IMAGE_QUALITY', 85),
        'thumbnail_width' => env('THUMBNAIL_WIDTH', 400),
        'thumbnail_height' => env('THUMBNAIL_HEIGHT', 300),
    ],
    
    'cache' => [
        'driver' => env('CACHE_DRIVER', 'file'),
        'ttl' => env('CACHE_TTL', 3600),
        'redis_host' => env('REDIS_HOST', '127.0.0.1'),
        'redis_port' => env('REDIS_PORT', 6379),
        'redis_password' => env('REDIS_PASSWORD'),
    ],
    
    'external_services' => [
        'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'yandex_maps_api_key' => env('YANDEX_MAPS_API_KEY'),
    ],
    
    'performance' => [
        'gzip_enabled' => env('GZIP_ENABLED', true),
        'assets_version' => env('ASSETS_VERSION', '1.0.0'),
        'cdn_url' => env('CDN_URL'),
    ],
    
    'development' => [
        'whoops_enabled' => env('WHOOPS_ENABLED', true),
        'query_log_enabled' => env('QUERY_LOG_ENABLED', false),
        'profiler_enabled' => env('PROFILER_ENABLED', false),
    ],
    
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],
];