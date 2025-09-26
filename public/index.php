<?php

/**
 * Real Estate System - Application Bootstrap
 *
 * Single entry point for all requests
 */

// Start output buffering with gzip compression if enabled
if (extension_loaded('zlib')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

// Load environment variables
require_once __DIR__ . '/../config/env.php';

// Load Composer autoloader (PSR-4 classes, helpers)
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration helpers
require_once __DIR__ . '/../config/helpers.php';

// Load authentication helpers
require_once __DIR__ . '/../app/Helpers/auth.php';

// Set error reporting based on environment
if (env('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Set default timezone
date_default_timezone_set(config('app.timezone', 'Europe/Moscow'));

try {
    // Create core instances
    $request = new \Core\Request();
    $router = new \Core\Router();

    // Setup CSRF protection (force session start / token generation)
    $csrf = new \Core\CSRF();

    // Load routes into the existing router instance
    require __DIR__ . '/../config/routes.php';

    // Dispatch request and get response
    $response = $router->dispatch($request);

    // Send response to browser
    $response->send();

} catch (\Throwable $e) {
    // Handle uncaught exceptions
    handleException($e, $request ?? null);
}

/**
 * Global exception handler
 */
function handleException(\Throwable $e, ?\Core\Request $request = null): void
{
    // Log the error
    if (function_exists('logger')) {
        logger()->error('Uncaught exception: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    } else {
        error_log('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    }

    // Clear any existing output
    if (ob_get_level()) {
        ob_clean();
    }

    // Set appropriate status code
    http_response_code(500);

    // Check if request expects JSON
    $expectsJson = $request && $request->expectsJson();

    if ($expectsJson) {
        // Return JSON error response
        header('Content-Type: application/json');

        if (env('APP_DEBUG', false)) {
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
            ]);
        } else {
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred',
            ]);
        }
    } else {
        // Return HTML error page
        if (env('APP_DEBUG', false)) {
            // Development error page
            echo "<!DOCTYPE html>\n";
            echo "<html lang=\"en\">\n<head>\n";
            echo "<meta charset=\"UTF-8\">\n";
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
            echo "<title>Application Error</title>\n";
            echo "<style>\n";
            echo "body { font-family: Arial, sans-serif; margin: 40px; background: #f8f9fa; }\n";
            echo ".error-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
            echo "h1 { color: #dc3545; margin-bottom: 20px; }\n";
            echo ".error-details { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0; }\n";
            echo "pre { background: #343a40; color: #fff; padding: 15px; border-radius: 4px; overflow: auto; }\n";
            echo "</style>\n</head>\n<body>\n";
            echo "<div class=\"error-container\">\n";
            echo "<h1>Application Error</h1>\n";
            echo "<div class=\"error-details\">\n";
            echo '<strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . "<br>\n";
            echo '<strong>File:</strong> ' . htmlspecialchars($e->getFile()) . "<br>\n";
            echo '<strong>Line:</strong> ' . $e->getLine() . "\n";
            echo "</div>\n";
            echo "<h3>Stack Trace:</h3>\n";
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . "</pre>\n";
            echo "</div>\n</body>\n</html>";
        } else {
            // Production error page
            $errorFile = __DIR__ . '/../app/Views/errors/500.php';
            if (file_exists($errorFile)) {
                include $errorFile;
            } else {
                echo "<!DOCTYPE html>\n";
                echo "<html lang=\"en\">\n<head>\n";
                echo "<meta charset=\"UTF-8\">\n";
                echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
                echo "<title>Server Error</title>\n";
                echo "<style>\n";
                echo "body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }\n";
                echo "h1 { color: #dc3545; }\n";
                echo "</style>\n</head>\n<body>\n";
                echo "<h1>500 - Internal Server Error</h1>\n";
                echo "<p>Sorry, something went wrong on our server.</p>\n";
                echo "<p><a href=\"/\">Return to Homepage</a></p>\n";
                echo "</body>\n</html>";
            }
        }
    }

    exit(1);
}
