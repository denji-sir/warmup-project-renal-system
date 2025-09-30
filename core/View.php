<?php

namespace Core;

/**
 * Simple template engine for PHP views
 */
class View
{
    private string $viewPath;
    private array $data = [];
    private array $sections = [];
    private ?string $layout = null;
    
    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../resources/views';
    }
    
    /**
     * Render view template
     */
    public function render(string $view, array $data = []): string
    {
        $this->data = array_merge($this->data, $data);
        
        $viewFile = $this->getViewFile($view);
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: {$view}");
        }
        
        // Start output buffering
        ob_start();
        
        // Extract data to variables
        extract($this->data);
        
        // Include the view file
        include $viewFile;
        
        $content = ob_get_clean();
        
        // If layout is set, render within layout
        if ($this->layout) {
            $layoutFile = $this->getViewFile("layouts/{$this->layout}");
            
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout not found: {$this->layout}");
            }
            
            ob_start();
            extract($this->data);
            include $layoutFile;
            $content = ob_get_clean();
            
            $this->layout = null; // Reset layout
        }
        
        return $content;
    }
    
    /**
     * Set layout template
     */
    public function layout(string $layout): void
    {
        $this->layout = $layout;
    }
    
    /**
     * Start a section
     */
    public function section(string $name): void
    {
        $this->sections[$name] = '';
        ob_start();
    }
    
    /**
     * End current section
     */
    public function endSection(): void
    {
        $sectionName = array_key_last($this->sections);
        if ($sectionName !== null) {
            $this->sections[$sectionName] = ob_get_clean();
        }
    }
    
    /**
     * Yield section content
     */
    public function yield(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }
    
    /**
     * Include partial view
     */
    public function include(string $partial, array $data = []): void
    {
        echo $this->partial($partial, $data);
    }
    
    /**
     * Render partial view
     */
    public function partial(string $partial, array $data = []): string
    {
        $partialFile = $this->getViewFile("partials/{$partial}");
        
        if (!file_exists($partialFile)) {
            throw new \Exception("Partial not found: {$partial}");
        }
        
        ob_start();
        extract(array_merge($this->data, $data));
        include $partialFile;
        return ob_get_clean();
    }
    
    /**
     * Set view data
     */
    public function with(string|array $key, mixed $value = null): self
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * Check if view exists
     */
    public function exists(string $view): bool
    {
        return file_exists($this->getViewFile($view));
    }
    
    /**
     * Get view file path
     */
    private function getViewFile(string $view): string
    {
        $view = str_replace('.', '/', $view);
        return $this->viewPath . '/' . $view . '.php';
    }
    
    /**
     * Static helper to render view
     */
    public static function make(string $view, array $data = []): self
    {
        $instance = new self();
        return $instance->with($data);
    }
    
    /**
     * Escape HTML entities (helper for views)
     */
    public function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Format date (helper for views)
     */
    public function formatDate(?string $date, string $format = 'Y-m-d H:i'): string
    {
        if (!$date) {
            return '';
        }
        
        try {
            return (new \DateTime($date))->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
    
    /**
     * Format price (helper for views)
     */
    public function formatPrice(float $price, string $currency = 'RUB'): string
    {
        return number_format($price, 0, '.', ' ') . ' ' . $currency;
    }
    
    /**
     * Truncate text (helper for views)
     */
    public function truncate(?string $text, int $length = 100, string $suffix = '...'): string
    {
        if (!$text || mb_strlen($text) <= $length) {
            return $text ?? '';
        }
        
        return mb_substr($text, 0, $length) . $suffix;
    }
    
    /**
     * Generate URL (helper for views)
     */
    public function url(string $path = ''): string
    {
        return url($path);
    }
    
    /**
     * Generate asset URL (helper for views)
     */
    public function asset(string $path): string
    {
        return asset($path);
    }
    
    /**
     * Get old input value (helper for forms)
     */
    public function old(string $key, mixed $default = ''): mixed
    {
        return old($key, $default);
    }
    
    /**
     * Get CSRF token (helper for forms)
     */
    public function csrfToken(): string
    {
        return csrf_token();
    }
    
    /**
     * Generate CSRF field (helper for forms)
     */
    public function csrfField(): string
    {
        return csrf_field();
    }
    
    /**
     * Generate method field (helper for forms)
     */
    public function methodField(string $method): string
    {
        return method_field($method);
    }
    
    /**
     * Get authenticated user (helper for views)
     */
    public function user(): ?\App\Models\User
    {
        return user();
    }
    
    /**
     * Check if user is authenticated (helper for views)
     */
    public function auth(): bool
    {
        return auth()->check();
    }
    
    /**
     * Translate string (helper for views)
     */
    public function __(string $key, array $replace = []): string
    {
        return __($key, $replace);
    }
}