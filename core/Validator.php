<?php

namespace Core;

/**
 * Input validation class
 */
class Validator
{
    private array $data = [];
    private array $rules = [];
    private array $messages = [];
    private array $errors = [];

    public function __construct(array $data = [], array $messages = [])
    {
        $this->setData($data);
        $this->messages = $messages;
    }

    /**
     * Set validation data
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Add custom error message manually
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field][] = $message;
        return $this;
    }

    /**
     * Required field validation (supports multiple fields)
     */
    public function required(array|string $fields, array $messages = []): self
    {
        foreach ((array) $fields as $field) {
            if ($this->isEmpty($this->data[$field] ?? null)) {
                $this->pushError($field, $messages[$field] ?? null, 'required');
            }
        }

        return $this;
    }

    /**
     * Validate email format
     */
    public function email(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;

        if (!$this->isEmpty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushError($field, $message, 'email');
        }

        return $this;
    }

    /**
     * Validate minimum string length
     */
    public function min(string $field, int $min, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;

        if (!$this->isEmpty($value) && strlen((string) $value) < $min) {
            $this->pushError($field, $message, 'min', ['min' => $min]);
        }

        return $this;
    }

    /**
     * Validate maximum string length
     */
    public function max(string $field, int $max, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;

        if (!$this->isEmpty($value) && strlen((string) $value) > $max) {
            $this->pushError($field, $message, 'max', ['max' => $max]);
        }

        return $this;
    }

    /**
     * Validate alphanumeric value
     */
    public function alphaNum(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;

        if (!$this->isEmpty($value) && !ctype_alnum((string) $value)) {
            $this->pushError($field, $message, 'alpha_num');
        }

        return $this;
    }

    /**
     * Validate confirmation field matches (field + "_confirmation")
     */
    public function confirmed(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        $confirmation = $this->data[$field . '_confirmation'] ?? null;

        if ($value !== $confirmation) {
            $this->pushError($field, $message, 'confirmed');
        }

        return $this;
    }

    /**
     * Check if validator has no errors
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Validate data against rules
     */
    public function validate(array $data, array $rules, array $messages = []): array
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $this->validateField($field, $ruleSet);
        }

        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'data' => $this->getValidatedData()
        ];
    }
    
    /**
     * Validate single field
     */
    private function validateField(string $field, string|array $rules): void
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        
        $value = $this->data[$field] ?? null;
        
        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }
    
    /**
     * Apply single validation rule
     */
    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $parameters] = $this->parseRule($rule);
        
        switch ($ruleName) {
            case 'required':
                if ($this->isEmpty($value)) {
                    $this->addRuleError($field, 'required');
                }
                break;
                
            case 'email':
                if (!$this->isEmpty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addRuleError($field, 'email');
                }
                break;
                
            case 'min':
                $min = (int) $parameters[0];
                if (!$this->isEmpty($value) && strlen($value) < $min) {
                    $this->addRuleError($field, 'min', ['min' => $min]);
                }
                break;
                
            case 'max':
                $max = (int) $parameters[0];
                if (!$this->isEmpty($value) && strlen($value) > $max) {
                    $this->addRuleError($field, 'max', ['max' => $max]);
                }
                break;
                
            case 'numeric':
                if (!$this->isEmpty($value) && !is_numeric($value)) {
                    $this->addRuleError($field, 'numeric');
                }
                break;

            case 'integer':
                if (!$this->isEmpty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addRuleError($field, 'integer');
                }
                break;

            case 'alpha_num':
                if (!$this->isEmpty($value) && !ctype_alnum((string) $value)) {
                    $this->addRuleError($field, 'alpha_num');
                }
                break;

            case 'url':
                if (!$this->isEmpty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addRuleError($field, 'url');
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmValue = $this->data[$confirmField] ?? null;
                if ($value !== $confirmValue) {
                    $this->addRuleError($field, 'confirmed');
                }
                break;
                
            case 'unique':
                if (!$this->isEmpty($value)) {
                    $table = $parameters[0] ?? null;
                    $column = $parameters[1] ?? $field;
                    $except = $parameters[2] ?? null;
                    
                    if ($this->isUnique($table, $column, $value, $except)) {
                        $this->addRuleError($field, 'unique');
                    }
                }
                break;
                
            case 'exists':
                if (!$this->isEmpty($value)) {
                    $table = $parameters[0] ?? null;
                    $column = $parameters[1] ?? $field;
                    
                    if (!$this->recordExists($table, $column, $value)) {
                        $this->addRuleError($field, 'exists');
                    }
                }
                break;
                
            case 'in':
                if (!$this->isEmpty($value) && !in_array($value, $parameters)) {
                    $this->addRuleError($field, 'in', ['values' => implode(', ', $parameters)]);
                }
                break;
                
            case 'file':
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                        $this->addRuleError($field, 'file');
                    }
                }
                break;
                
            case 'image':
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $imageInfo = getimagesize($_FILES[$field]['tmp_name']);
                    if (!$imageInfo) {
                        $this->addRuleError($field, 'image');
                    }
                }
                break;
                
            case 'mimes':
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                    if (!in_array($extension, $parameters)) {
                        $this->addRuleError($field, 'mimes', ['mimes' => implode(', ', $parameters)]);
                    }
                }
                break;
                
            case 'max_size':
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $maxSize = (int) $parameters[0] * 1024 * 1024; // Convert MB to bytes
                    if ($_FILES[$field]['size'] > $maxSize) {
                        $this->addRuleError($field, 'max_size', ['max' => $parameters[0]]);
                    }
                }
                break;
        }
    }
    
    /**
     * Parse rule string
     */
    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$name, $params] = explode(':', $rule, 2);
            $parameters = explode(',', $params);
        } else {
            $name = $rule;
            $parameters = [];
        }
        
        return [$name, $parameters];
    }
    
    /**
     * Check if value is empty
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }
    
    /**
     * Check if value is unique in database
     */
    private function isUnique(string $table, string $column, mixed $value, ?string $except = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $params = [$value];
        
        if ($except) {
            $sql .= " AND id != ?";
            $params[] = $except;
        }
        
        $result = DB::fetch($sql, $params);
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Check if record exists in database
     */
    private function recordExists(string $table, string $column, mixed $value): bool
    {
        $result = DB::fetch(
            "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?",
            [$value]
        );
        
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Add validation error
     */
    private function addRuleError(string $field, string $rule, array $parameters = []): void
    {
        $key = "{$field}.{$rule}";

        if (isset($this->messages[$key])) {
            $message = $this->messages[$key];
        } else {
            $message = $this->getDefaultMessage($field, $rule, $parameters);
        }
        
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get default error message
     */
    private function getDefaultMessage(string $field, string $rule, array $parameters = []): string
    {
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        
        $messages = [
            'required' => "{$fieldName} is required",
            'email' => "{$fieldName} must be a valid email address",
            'min' => "{$fieldName} must be at least {$parameters['min']} characters",
            'max' => "{$fieldName} must not exceed {$parameters['max']} characters",
            'numeric' => "{$fieldName} must be a number",
            'integer' => "{$fieldName} must be an integer",
            'url' => "{$fieldName} must be a valid URL",
            'confirmed' => "{$fieldName} confirmation does not match",
            'unique' => "{$fieldName} is already taken",
            'exists' => "Selected {$fieldName} does not exist",
            'alpha_num' => "{$fieldName} may only contain letters and numbers",
            'in' => "{$fieldName} must be one of: {$parameters['values']}",
            'file' => "{$fieldName} must be a valid file",
            'image' => "{$fieldName} must be a valid image",
            'mimes' => "{$fieldName} must be of type: {$parameters['mimes']}",
            'max_size' => "{$fieldName} must not exceed {$parameters['max']} MB"
        ];

        return $messages[$rule] ?? "{$fieldName} is invalid";
    }

    /**
     * Add error using custom message or default rule message
     */
    private function pushError(string $field, ?string $customMessage, string $rule, array $parameters = []): void
    {
        if ($customMessage !== null) {
            $this->errors[$field][] = $customMessage;
            return;
        }

        $this->addRuleError($field, $rule, $parameters);
    }
    
    /**
     * Get validated data (only fields that passed validation)
     */
    private function getValidatedData(): array
    {
        $validated = [];
        
        foreach (array_keys($this->rules) as $field) {
            if (!isset($this->errors[$field]) && array_key_exists($field, $this->data)) {
                $validated[$field] = $this->data[$field];
            }
        }
        
        return $validated;
    }
    
    /**
     * Static helper method
     */
    public static function make(array $data, array $rules, array $messages = []): array
    {
        $validator = new static();
        return $validator->validate($data, $rules, $messages);
    }
}
