<?php

/**
 * Simple test script to check authentication system
 * Access via /test-auth.php
 */

// Include the application bootstrap
require_once __DIR__ . '/index.php';

echo "<h1>Тест системы аутентификации</h1>";

// Test 1: Check if classes exist
echo "<h2>1. Проверка классов</h2>";
if (class_exists('Core\Auth')) {
    echo "✅ Класс Core\Auth найден<br>";
} else {
    echo "❌ Класс Core\Auth не найден<br>";
}

if (class_exists('App\Models\User')) {
    echo "✅ Класс App\Models\User найден<br>";
} else {
    echo "❌ Класс App\Models\User не найден<br>";
}

if (class_exists('Core\CSRF')) {
    echo "✅ Класс Core\CSRF найден<br>";
} else {
    echo "❌ Класс Core\CSRF не найден<br>";
}

// Test 2: Check helper functions
echo "<h2>2. Проверка helper-функций</h2>";
if (function_exists('csrf_token')) {
    echo "✅ Функция csrf_token() работает: " . csrf_token() . "<br>";
} else {
    echo "❌ Функция csrf_token() не найдена<br>";
}

if (function_exists('isLoggedIn')) {
    echo "✅ Функция isLoggedIn() работает: " . (isLoggedIn() ? 'true' : 'false') . "<br>";
} else {
    echo "❌ Функция isLoggedIn() не найдена<br>";
}

// Test 3: Check database connection
echo "<h2>3. Проверка подключения к базе данных</h2>";
try {
    $db = new Core\Database();
    $pdo = $db->getConnection();
    if ($pdo instanceof PDO) {
        echo "✅ Подключение к базе данных установлено<br>";
        
        // Check if users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Таблица users существует<br>";
            
            // Check table structure
            $stmt = $pdo->query("DESCRIBE users");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "✅ Колонки таблицы users: " . implode(', ', $columns) . "<br>";
        } else {
            echo "❌ Таблица users не найдена<br>";
        }
    } else {
        echo "❌ Не удалось установить подключение к базе данных<br>";
    }
} catch (Exception $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "<br>";
}

// Test 4: Check session
echo "<h2>4. Проверка сессий</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Сессия активна<br>";
} else {
    echo "❌ Сессия не активна<br>";
}

// Test 5: Check configuration
echo "<h2>5. Проверка конфигурации</h2>";
if (file_exists(__DIR__ . '/../config/auth.php')) {
    echo "✅ Файл конфигурации аутентификации найден<br>";
    $authConfig = require __DIR__ . '/../config/auth.php';
    echo "✅ Настройки загружены: " . count($authConfig) . " параметров<br>";
} else {
    echo "❌ Файл конфигурации аутентификации не найден<br>";
}

// Test 6: Check views
echo "<h2>6. Проверка шаблонов</h2>";
$templates = ['login', 'register', 'forgot-password', 'reset-password', 'verify-email'];
foreach ($templates as $template) {
    $path = __DIR__ . "/../resources/views/auth/$template.php";
    if (file_exists($path)) {
        echo "✅ Шаблон $template.php найден<br>";
    } else {
        echo "❌ Шаблон $template.php не найден<br>";
    }
}

echo "<h2>Тест завершен</h2>";
echo "<p><a href='/'>На главную</a> | <a href='/login'>Войти</a> | <a href='/register'>Регистрация</a></p>";