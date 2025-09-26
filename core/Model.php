<?php

namespace Core;

/**
 * Base Model class with Active Record pattern
 */
abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected array $dates = ['created_at', 'updated_at'];
    protected bool $timestamps = true;
    
    protected array $attributes = [];
    protected bool $exists = false;
    
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }
    
    /**
     * Fill model attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        
        return $this;
    }
    
    /**
     * Check if attribute is fillable
     */
    protected function isFillable(string $key): bool
    {
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable);
        }
        
        if (!empty($this->guarded)) {
            return !in_array($key, $this->guarded);
        }
        
        return true;
    }
    
    /**
     * Set attribute value
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get attribute value
     */
    public function getAttribute(string $key): mixed
    {
        $value = $this->attributes[$key] ?? null;
        
        // Apply casting
        if (isset($this->casts[$key])) {
            return $this->castAttribute($key, $value);
        }
        
        return $value;
    }
    
    /**
     * Cast attribute to specified type
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }
        
        return match ($this->casts[$key]) {
            'int', 'integer' => (int) $value,
            'float' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string' => (string) $value,
            'array' => is_string($value) ? json_decode($value, true) : $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            'datetime' => new \DateTime($value),
            default => $value
        };
    }
    
    /**
     * Magic getter
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }
    
    /**
     * Magic setter
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }
    
    /**
     * Magic isset
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
    
    /**
     * Get table name
     */
    public function getTable(): string
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
        
        return $this->table;
    }
    
    /**
     * Get primary key
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }
    
    /**
     * Get primary key value
     */
    public function getKey(): mixed
    {
        return $this->getAttribute($this->getKeyName());
    }
    
    /**
     * Save model to database
     */
    public function save(): bool
    {
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            
            if (!$this->exists) {
                $this->setAttribute('created_at', $now);
            }
            
            $this->setAttribute('updated_at', $now);
        }
        
        if ($this->exists) {
            return $this->performUpdate();
        } else {
            return $this->performInsert();
        }
    }
    
    /**
     * Perform insert operation
     */
    protected function performInsert(): bool
    {
        $attributes = $this->getAttributesForInsert();
        
        try {
            $id = DB::insert($this->getTable(), $attributes);
            
            if ($id) {
                $this->setAttribute($this->getKeyName(), $id);
                $this->exists = true;
                return true;
            }
        } catch (\Exception $e) {
            logger("Model insert failed: " . $e->getMessage(), 'error');
        }
        
        return false;
    }
    
    /**
     * Perform update operation
     */
    protected function performUpdate(): bool
    {
        $attributes = $this->getAttributesForUpdate();
        $key = $this->getKey();
        
        if (!$key) {
            return false;
        }
        
        try {
            $affected = DB::update(
                $this->getTable(),
                $attributes,
                [$this->getKeyName() => $key]
            );
            
            return $affected > 0;
        } catch (\Exception $e) {
            logger("Model update failed: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Get attributes for insert
     */
    protected function getAttributesForInsert(): array
    {
        $attributes = $this->attributes;
        
        // Remove primary key if it's auto-increment
        if (isset($attributes[$this->getKeyName()]) && !$attributes[$this->getKeyName()]) {
            unset($attributes[$this->getKeyName()]);
        }
        
        return $this->prepareDatabaseAttributes($attributes);
    }
    
    /**
     * Get attributes for update
     */
    protected function getAttributesForUpdate(): array
    {
        $attributes = $this->attributes;
        
        // Remove primary key from update data
        unset($attributes[$this->getKeyName()]);
        
        return $this->prepareDatabaseAttributes($attributes);
    }
    
    /**
     * Prepare attributes for database storage
     */
    protected function prepareDatabaseAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            // Convert arrays/objects to JSON
            if (isset($this->casts[$key]) && in_array($this->casts[$key], ['array', 'json'])) {
                $attributes[$key] = json_encode($value);
            }
        }
        
        return $attributes;
    }
    
    /**
     * Delete model from database
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        $key = $this->getKey();
        if (!$key) {
            return false;
        }
        
        try {
            $affected = DB::delete($this->getTable(), [$this->getKeyName() => $key]);
            
            if ($affected > 0) {
                $this->exists = false;
                return true;
            }
        } catch (\Exception $e) {
            logger("Model delete failed: " . $e->getMessage(), 'error');
        }
        
        return false;
    }
    
    /**
     * Find model by primary key
     */
    public static function find(mixed $id): ?static
    {
        $instance = new static();
        
        $result = DB::fetch(
            "SELECT * FROM {$instance->getTable()} WHERE {$instance->getKeyName()} = ?",
            [$id]
        );
        
        if ($result) {
            $model = new static($result);
            $model->exists = true;
            return $model;
        }
        
        return null;
    }
    
    /**
     * Find or fail
     */
    public static function findOrFail(mixed $id): static
    {
        $model = static::find($id);
        
        if (!$model) {
            throw new \Exception("Model not found with ID: {$id}");
        }
        
        return $model;
    }
    
    /**
     * Get all records
     */
    public static function all(): array
    {
        $instance = new static();
        $results = DB::fetchAll("SELECT * FROM {$instance->getTable()}");
        
        return array_map(function ($row) {
            $model = new static($row);
            $model->exists = true;
            return $model;
        }, $results);
    }
    
    /**
     * Create new model
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }
    
    /**
     * Simple where query
     */
    public static function where(string $column, mixed $operator, mixed $value = null): array
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $instance = new static();
        $results = DB::fetchAll(
            "SELECT * FROM {$instance->getTable()} WHERE {$column} {$operator} ?",
            [$value]
        );
        
        return array_map(function ($row) {
            $model = new static($row);
            $model->exists = true;
            return $model;
        }, $results);
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        $array = [];
        
        foreach ($this->attributes as $key => $value) {
            $array[$key] = $this->getAttribute($key);
        }
        
        return $array;
    }
    
    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}