<?php

/**
 * Database Migration Runner
 * 
 * Этот скрипт выполняет SQL миграции в правильном порядке.
 * Запуск: php database/migrate.php
 */

// Загрузка конфигурации
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/helpers.php';

class DatabaseMigrator
{
    private PDO $connection;
    private string $migrationsPath;
    private array $executedMigrations = [];

    public function __construct()
    {
        $this->migrationsPath = __DIR__ . '/migrations';
        $this->connect();
        $this->createMigrationsTable();
        $this->loadExecutedMigrations();
    }

    /**
     * Подключение к базе данных
     */
    private function connect(): void
    {
        $host = config('database.host', 'localhost');
        $port = config('database.port', 3306);
        $name = config('database.name', 'realestate_db');
        $user = config('database.user', 'root');
        $password = config('database.password', '');
        $charset = config('database.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};charset={$charset}";

        try {
            // Сначала подключаемся без указания базы данных
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
            ]);

            echo "✓ Подключение к MySQL установлено\n";

            // Создаем базу данных если она не существует
            $this->connection->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET {$charset} COLLATE utf8mb4_0900_ai_ci");
            echo "✓ База данных '{$name}' создана или уже существует\n";

            // Переподключаемся с указанием базы данных
            $dsnWithDb = "{$dsn};dbname={$name}";
            $this->connection = new PDO($dsnWithDb, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
            ]);

            echo "✓ Подключение к базе данных '{$name}' установлено\n";

        } catch (PDOException $e) {
            echo "✗ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
            echo "Убедитесь что:\n";
            echo "- MySQL сервер запущен\n";
            echo "- Настройки подключения в .env файле корректны\n";
            echo "- У пользователя есть права на создание базы данных\n";
            exit(1);
        }
    }

    /**
     * Создание таблицы для отслеживания миграций
     */
    private function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `migrations` (
                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `migration` varchar(255) NOT NULL,
                `batch` int NOT NULL,
                `executed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
        ";

        $this->connection->exec($sql);
        echo "✓ Таблица миграций готова\n";
    }

    /**
     * Загрузка уже выполненных миграций
     */
    private function loadExecutedMigrations(): void
    {
        $stmt = $this->connection->prepare("SELECT migration FROM migrations ORDER BY id");
        $stmt->execute();
        
        $this->executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($this->executedMigrations)) {
            echo "✓ Найдено " . count($this->executedMigrations) . " выполненных миграций\n";
        }
    }

    /**
     * Получение списка файлов миграций
     */
    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            echo "✗ Папка миграций не найдена: {$this->migrationsPath}\n";
            exit(1);
        }

        $files = glob($this->migrationsPath . '/*.sql');
        
        if (empty($files)) {
            echo "! Файлы миграций не найдены\n";
            return [];
        }

        // Сортируем файлы по имени (они должны быть пронумерованы)
        sort($files);

        return array_map(function ($file) {
            return basename($file, '.sql');
        }, $files);
    }

    /**
     * Выполнение миграций
     */
    public function migrate(): void
    {
        echo "\n=== Запуск миграций базы данных ===\n\n";

        $migrations = $this->getMigrationFiles();
        $pendingMigrations = array_diff($migrations, $this->executedMigrations);

        if (empty($pendingMigrations)) {
            echo "✓ Все миграции уже выполнены\n";
            return;
        }

        echo "Найдено " . count($pendingMigrations) . " новых миграций:\n";
        foreach ($pendingMigrations as $migration) {
            echo "  - {$migration}\n";
        }
        echo "\n";

        $batch = $this->getNextBatch();

        foreach ($pendingMigrations as $migration) {
            $this->executeMigration($migration, $batch);
        }

        echo "\n✅ Все миграции успешно выполнены!\n";
    }

    /**
     * Выполнение одной миграции
     */
    private function executeMigration(string $migration, int $batch): void
    {
        $filePath = $this->migrationsPath . '/' . $migration . '.sql';

        if (!file_exists($filePath)) {
            echo "✗ Файл миграции не найден: {$filePath}\n";
            return;
        }

        echo "Выполнение: {$migration}... ";

        try {
            // Читаем содержимое файла
            $sql = file_get_contents($filePath);

            if (empty($sql)) {
                echo "✗ Файл миграции пуст\n";
                return;
            }

            // Выполняем SQL без транзакции для DDL операций
            $this->connection->exec($sql);

            // Записываем выполненную миграцию
            $stmt = $this->connection->prepare(
                "INSERT INTO migrations (migration, batch) VALUES (?, ?)"
            );
            $stmt->execute([$migration, $batch]);

            echo "✓ ВЫПОЛНЕНА\n";

        } catch (Exception $e) {
            echo "✗ ОШИБКА\n";
            echo "   Сообщение: " . $e->getMessage() . "\n";
            echo "   Миграция остановлена\n";
            exit(1);
        }
    }

    /**
     * Получение номера следующего батча
     */
    private function getNextBatch(): int
    {
        $stmt = $this->connection->prepare("SELECT MAX(batch) as max_batch FROM migrations");
        $stmt->execute();
        $result = $stmt->fetch();

        return ($result['max_batch'] ?? 0) + 1;
    }

    /**
     * Откат миграций
     */
    public function rollback(int $steps = 1): void
    {
        echo "\n=== Откат миграций ===\n\n";

        $stmt = $this->connection->prepare(
            "SELECT DISTINCT batch FROM migrations ORDER BY batch DESC LIMIT ?"
        );
        $stmt->execute([$steps]);
        $batches = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($batches)) {
            echo "! Нет миграций для отката\n";
            return;
        }

        $batchesStr = implode(', ', $batches);
        $stmt = $this->connection->prepare(
            "SELECT migration FROM migrations WHERE batch IN ({$batchesStr}) ORDER BY id DESC"
        );
        $stmt->execute();
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "К откату: " . count($migrations) . " миграций\n\n";

        foreach ($migrations as $migration) {
            echo "⚠️  ВНИМАНИЕ: Автоматический откат не реализован\n";
            echo "   Миграция: {$migration}\n";
            echo "   Требуется ручной откат изменений в базе данных\n";
        }

        echo "\n! Для полного отката удалите таблицы и выполните миграции заново\n";
    }

    /**
     * Показать статус миграций
     */
    public function status(): void
    {
        echo "\n=== Статус миграций ===\n\n";

        $allMigrations = $this->getMigrationFiles();
        $executed = $this->executedMigrations;

        if (empty($allMigrations)) {
            echo "! Файлы миграций не найдены\n";
            return;
        }

        echo sprintf("%-50s %s\n", "Миграция", "Статус");
        echo str_repeat("-", 70) . "\n";

        foreach ($allMigrations as $migration) {
            $status = in_array($migration, $executed) ? "✓ Выполнена" : "⏳ Ожидает";
            echo sprintf("%-50s %s\n", $migration, $status);
        }

        $pending = count($allMigrations) - count($executed);
        echo "\nВсего: " . count($allMigrations) . ", Выполнено: " . count($executed) . ", Ожидает: {$pending}\n";
    }
}

// Обработка аргументов командной строки
$command = $argv[1] ?? 'migrate';

try {
    $migrator = new DatabaseMigrator();

    switch ($command) {
        case 'migrate':
            $migrator->migrate();
            break;

        case 'rollback':
            $steps = (int)($argv[2] ?? 1);
            $migrator->rollback($steps);
            break;

        case 'status':
            $migrator->status();
            break;

        case 'help':
        default:
            echo "\n=== Database Migrator ===\n\n";
            echo "Использование:\n";
            echo "  php database/migrate.php [команда]\n\n";
            echo "Доступные команды:\n";
            echo "  migrate              Выполнить все новые миграции (по умолчанию)\n";
            echo "  rollback [steps]     Откатить миграции (по умолчанию 1 шаг)\n";
            echo "  status               Показать статус всех миграций\n";
            echo "  help                 Показать эту справку\n\n";
            echo "Примеры:\n";
            echo "  php database/migrate.php\n";
            echo "  php database/migrate.php status\n";
            echo "  php database/migrate.php rollback 2\n\n";
            break;
    }

} catch (Exception $e) {
    echo "✗ Критическая ошибка: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}