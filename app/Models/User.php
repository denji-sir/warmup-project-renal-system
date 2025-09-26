<?php

namespace App\Models;

use Core\DB;
use Core\Model;

class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'username',
        'first_name',
        'last_name', 
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'email_verified_at',
        'email_verification_token',
        'password_reset_token',
        'password_reset_expires',
        'login_attempts',
        'locked_until',
        'last_login_at',
        'avatar',
        'bio',
        'company',
        'license_number',
        'preferences'
    ];

    protected array $hidden = [
        'password',
        'password_reset_token',
        'email_verification_token',
        'remember_token'
    ];

    protected array $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create user with validation
     */
    public function createUser(array $data): int|false
    {
        // Automatically add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Check uniqueness before creating
        if (isset($data['email']) && $this->findByEmail($data['email'])) {
            return false;
        }

        if (isset($data['username']) && $this->findByUsername($data['username'])) {
            return false;
        }

        $insertId = DB::insert($this->table, $data);

        return $insertId ? (int) $insertId : false;
    }

    /**
     * Update user with automatic timestamp
     */
    public function updateUser(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return DB::update($this->table, $data, [$this->primaryKey => $id]) > 0;
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Get user's avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return '/storage/avatars/' . $this->avatar;
        }
        
        // Generate avatar based on initials
        return $this->generateAvatarUrl();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        return in_array($this->role, $roles);
    }

    /**
     * Check if user can perform action
     */
    public function can(string $permission): bool
    {
        return match ($this->role) {
            'admin' => true, // Admin can do everything
            'realtor' => in_array($permission, [
                'create_property',
                'edit_own_property',
                'view_properties',
                'manage_clients',
                'view_dashboard'
            ]),
            'tenant' => in_array($permission, [
                'view_properties',
                'favorite_properties',
                'contact_realtor',
                'submit_applications'
            ]),
            default => false
        };
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Verify user's password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Update user's password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->updateUser($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $result = DB::fetch($sql, [$email]);
        return $result ?: null;
    }

    /**
     * Find user by username
     */
    public static function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $result = DB::fetch($sql, [$username]);
        return $result ?: null;
    }

    /**
     * Find user by password reset token
     */
    public static function findByPasswordResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE password_reset_token = ? LIMIT 1";
        $result = DB::fetch($sql, [$token]);
        return $result ?: null;
    }

    /**
     * Find user by email verification token
     */
    public static function findByEmailVerificationToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE email_verification_token = ? LIMIT 1";
        $result = DB::fetch($sql, [$token]);
        return $result ?: null;
    }

    /**
     * Get active users
     */
    public static function getActive(): array
    {
        $sql = "SELECT * FROM users WHERE is_active = 1";
        return DB::fetchAll($sql);
    }

    /**
     * Get users by role
     */
    public static function getByRole(string $role): array
    {
        $sql = "SELECT * FROM users WHERE role = ? AND is_active = 1";
        return DB::fetchAll($sql, [$role]);
    }

    /**
     * Get realtors
     */
    public function getRealtors(): array
    {
        return self::getByRole('realtor');
    }

    /**
     * Get tenants  
     */
    public function getTenants(): array
    {
        return self::getByRole('tenant');
    }

    /**
     * Get user's properties (for realtors)
     */
    public function properties()
    {
        if ($this->role !== 'realtor') {
            return [];
        }

        return [];
    }

    /**
     * Get user's favorite properties (for tenants)
     */
    public function favoriteProperties()
    {
        // return $this->belongsToMany(Property::class, 'user_favorites', 'user_id', 'property_id');
        return [];
    }

    /**
     * Get user's property applications (for tenants)
     */
    public function applications()
    {
        if ($this->role !== 'tenant') {
            return [];
        }

        return [];
    }

    /**
     * Generate avatar URL from initials
     */
    protected function generateAvatarUrl(): string
    {
        $initials = $this->initials;
        $background = $this->getColorFromString($this->email);
        
        // Use a service like UI Avatars or generate locally
        return "https://ui-avatars.com/api/?name={$initials}&background={$background}&color=fff&size=200";
    }

    /**
     * Generate color from string
     */
    protected function getColorFromString(string $string): string
    {
        $colors = [
            '6366f1', '8b5cf6', 'ec4899', 'ef4444', 'f97316',
            'eab308', '22c55e', '10b981', '06b6d4', '3b82f6'
        ];

        $hash = crc32($string);
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Get user's activity log
     */
    public function getActivityLog(int $limit = 50): array
    {
        // In a real implementation, you would have an activity log table
        return [
            [
                'action' => 'login',
                'description' => 'Вход в систему',
                'created_at' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]
        ];
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        $stats = [
            'properties_count' => 0,
            'favorites_count' => 0,
            'applications_count' => 0,
            'profile_completion' => $this->calculateProfileCompletion()
        ];

        if ($this->role === 'realtor') {
            // $stats['properties_count'] = $this->properties()->count();
            // $stats['active_properties'] = $this->properties()->active()->count();
            // $stats['deals_count'] = Deal::where('realtor_id', $this->id)->count();
        }

        if ($this->role === 'tenant') {
            // $stats['favorites_count'] = $this->favoriteProperties()->count();
            // $stats['applications_count'] = $this->applications()->count();
        }

        return $stats;
    }

    /**
     * Calculate profile completion percentage
     */
    protected function calculateProfileCompletion(): int
    {
        $fields = [
            'first_name', 'last_name', 'email', 'phone', 'bio'
        ];

        if ($this->role === 'realtor') {
            $fields[] = 'company';
            $fields[] = 'license_number';
        }

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return (int) (($completed / count($fields)) * 100);
    }

    /**
     * Serialize for JSON
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        
        // Remove sensitive data
        unset($data['password'], $data['remember_token']);
        
        // Add computed attributes
        $data['full_name'] = $this->full_name;
        $data['initials'] = $this->initials;
        $data['avatar_url'] = $this->avatar_url;
        $data['is_active'] = $this->isActive();
        $data['is_email_verified'] = $this->isEmailVerified();

        return $data;
    }
}
