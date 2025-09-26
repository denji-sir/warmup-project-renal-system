<?php

namespace Core;

/**
 * Simple PSR-3 compatible logger
 */
class Logger
{
    private string $logPath;
    private string $errorLogPath;
    private string $auditLogPath;
    
    private const LEVELS = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    ];
    
    public function __construct()
    {
        $this->logPath = env('LOG_PATH', __DIR__ . '/../storage/logs/app.log');
        $this->errorLogPath = env('ERROR_LOG_PATH', __DIR__ . '/../storage/logs/error.log');
        $this->auditLogPath = env('AUDIT_LOG_PATH', __DIR__ . '/../storage/logs/audit.log');
        
        // Ensure log directory exists
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Log message with level
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $minLevel = env('LOG_LEVEL', 'debug');
        
        if (self::LEVELS[$level] > self::LEVELS[$minLevel]) {
            return;
        }
        
        $logEntry = $this->formatMessage($level, $message, $context);
        
        // Write to main log
        $this->writeToFile($this->logPath, $logEntry);
        
        // Write errors to separate file
        if (in_array($level, ['emergency', 'alert', 'critical', 'error'])) {
            $this->writeToFile($this->errorLogPath, $logEntry);
        }
    }
    
    /**
     * Emergency: system is unusable
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
    
    /**
     * Alert: action must be taken immediately
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }
    
    /**
     * Critical: critical conditions
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    
    /**
     * Error: error conditions
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    
    /**
     * Warning: warning conditions
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    
    /**
     * Notice: normal but significant condition
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }
    
    /**
     * Info: informational messages
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    
    /**
     * Debug: debug-level messages
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
    
    /**
     * Log audit event
     */
    public function audit(string $action, array $data = []): void
    {
        $auditEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => auth()->id(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'action' => $action,
            'data' => $data
        ];
        
        $logEntry = json_encode($auditEntry) . PHP_EOL;
        $this->writeToFile($this->auditLogPath, $logEntry);
    }
    
    /**
     * Format log message
     */
    private function formatMessage(string $level, string $message, array $context = []): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        $contextString = $context ? ' ' . json_encode($context) : '';
        
        return "[{$timestamp}] {$levelUpper}: {$message}{$contextString}" . PHP_EOL;
    }
    
    /**
     * Write to log file
     */
    private function writeToFile(string $filePath, string $content): void
    {
        try {
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            file_put_contents($filePath, $content, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // Can't log the logging error, so just ignore it
            // In production, you might want to use error_log() here
            error_log("Logger write failed: " . $e->getMessage());
        }
    }
    
    /**
     * Clear log files
     */
    public function clear(): void
    {
        foreach ([$this->logPath, $this->errorLogPath] as $file) {
            if (file_exists($file)) {
                file_put_contents($file, '');
            }
        }
    }
    
    /**
     * Get log contents
     */
    public function getLog(string $type = 'app', int $lines = 100): array
    {
        $filePath = match ($type) {
            'error' => $this->errorLogPath,
            'audit' => $this->auditLogPath,
            default => $this->logPath
        };
        
        if (!file_exists($filePath)) {
            return [];
        }
        
        $content = file_get_contents($filePath);
        $logLines = explode("\n", trim($content));
        
        // Return last N lines
        return array_slice($logLines, -$lines);
    }
    
    /**
     * Rotate log files
     */
    public function rotate(): void
    {
        foreach ([$this->logPath, $this->errorLogPath, $this->auditLogPath] as $file) {
            if (file_exists($file) && filesize($file) > 10 * 1024 * 1024) { // 10MB
                $rotatedFile = $file . '.' . date('Y-m-d-H-i-s');
                rename($file, $rotatedFile);
                
                // Compress old log
                if (function_exists('gzopen')) {
                    $this->compressFile($rotatedFile);
                }
            }
        }
    }
    
    /**
     * Compress log file
     */
    private function compressFile(string $filePath): void
    {
        $gzFile = $filePath . '.gz';
        
        $fp = fopen($filePath, 'rb');
        $gzfp = gzopen($gzFile, 'wb9');
        
        while (!feof($fp)) {
            gzwrite($gzfp, fread($fp, 1024));
        }
        
        fclose($fp);
        gzclose($gzfp);
        
        unlink($filePath);
    }
}