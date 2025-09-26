<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database Connection Factory and Query Builder
 */
class DB
{
    private static ?PDO $connection = null;
    
    /**
     * Get database connection (singleton pattern)
     */
    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::connect();
        }
        
        return self::$connection;
    }
    
    /**
     * Establish database connection
     */
    private static function connect(): void
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $dbname = env('DB_NAME', 'realestate');
        $charset = env('DB_CHARSET', 'utf8mb4');
        $collation = env('DB_COLLATION', 'utf8mb4_0900_ai_ci');
        
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$collation}",
        ];
        
        try {
            self::$connection = new PDO(
                $dsn,
                env('DB_USER', 'root'),
                env('DB_PASS', ''),
                $options
            );
        } catch (PDOException $e) {
            logger("Database connection failed: " . $e->getMessage(), 'error');
            throw new \Exception('Database connection failed');
        }
    }
    
    /**
     * Execute a raw query
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = self::connection();
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            if (env('APP_DEBUG') && env('QUERY_LOG_ENABLED')) {
                logger("SQL: {$sql} | Params: " . json_encode($params));
            }
            
            return $stmt;
        } catch (PDOException $e) {
            logger("Query failed: {$sql} | Error: " . $e->getMessage(), 'error');
            throw $e;
        }
    }
    
    /**
     * Fetch single row
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Fetch all rows
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert record and return last insert ID
     */
    public static function insert(string $table, array $data): int|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        self::query($sql, $data);
        return self::connection()->lastInsertId();
    }
    
    /**
     * Update records
     */
    public static function update(string $table, array $data, array $where): int
    {
        $setPairs = [];
        foreach (array_keys($data) as $column) {
            $setPairs[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setPairs);
        
        $wherePairs = [];
        $whereData = [];
        foreach ($where as $column => $value) {
            $whereKey = "where_{$column}";
            $wherePairs[] = "{$column} = :{$whereKey}";
            $whereData[$whereKey] = $value;
        }
        $whereClause = implode(' AND ', $wherePairs);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$whereClause}";
        $params = array_merge($data, $whereData);
        
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Delete records
     */
    public static function delete(string $table, array $where): int
    {
        $wherePairs = [];
        foreach (array_keys($where) as $column) {
            $wherePairs[] = "{$column} = :{$column}";
        }
        $whereClause = implode(' AND ', $wherePairs);
        
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";
        
        $stmt = self::query($sql, $where);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::connection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::connection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::connection()->rollBack();
    }
    
    /**
     * Execute callback within transaction
     */
    public static function transaction(callable $callback): mixed
    {
        self::beginTransaction();
        
        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Exception $e) {
            self::rollback();
            throw $e;
        }
    }
    
    /**
     * Check if table exists
     */
    public static function tableExists(string $table): bool
    {
        $sql = "SHOW TABLES LIKE :table";
        $result = self::fetch($sql, ['table' => $table]);
        return $result !== null;
    }
    
    /**
     * Get table columns
     */
    public static function getColumns(string $table): array
    {
        $sql = "DESCRIBE {$table}";
        return self::fetchAll($sql);
    }
}