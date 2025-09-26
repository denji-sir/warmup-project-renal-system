<?php

namespace Core;

/**
 * HTTP Response wrapper
 */
class Response
{
    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];
    
    /**
     * Set response content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Get response content
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }
    
    /**
     * Get HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Set header
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Set multiple headers
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }
    
    /**
     * Get header
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
    
    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    /**
     * Send response to browser
     */
    public function send(): void
    {
        // Send status code
        http_response_code($this->statusCode);
        
        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        // Send content
        echo $this->content;
    }
    
    /**
     * Create JSON response
     */
    public static function json(mixed $data, int $status = 200): self
    {
        $response = new self();
        return $response
            ->setContent(json_encode($data))
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'application/json');
    }
    
    /**
     * Create HTML response
     */
    public static function html(string $content, int $status = 200): self
    {
        $response = new self();
        return $response
            ->setContent($content)
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8');
    }
    
    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $status = 302): self
    {
        $response = new self();
        return $response
            ->setStatusCode($status)
            ->setHeader('Location', $url);
    }
    
    /**
     * Create download response
     */
    public static function download(string $filePath, ?string $filename = null): self
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
        
        $filename = $filename ?: basename($filePath);
        $content = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
        $response = new self();
        return $response
            ->setContent($content)
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setHeader('Content-Length', (string)strlen($content));
    }
    
    /**
     * Create file response (inline)
     */
    public static function file(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
        
        $content = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
        $response = new self();
        return $response
            ->setContent($content)
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Length', (string)strlen($content));
    }
    
    /**
     * Create error response
     */
    public static function error(int $status, string $message = ''): self
    {
        $response = new self();
        return $response
            ->setStatusCode($status)
            ->setContent($message ?: "Error {$status}");
    }
    
    /**
     * Create 404 Not Found response
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::error(404, $message);
    }
    
    /**
     * Create 403 Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error(403, $message);
    }
    
    /**
     * Create 500 Internal Server Error response
     */
    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return self::error(500, $message);
    }
}