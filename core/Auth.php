<?php

namespace Core;

use App\Models\User;

/**
 * Authentication and authorization class
 */
class Auth
{
    private const SESSION_KEY = '_auth_user_id';
    private const REMEMBER_TOKEN_KEY = '_remember_token';
    private ?array $user = null;
    private Session $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    /**
     * Attempt to authenticate user
     */
    public function attempt(string $email, string $password, bool $remember = false): bool|string
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Check if user is active
        if (!$user['is_active']) {
            return 'inactive';
        }
        
        if (password_verify($password, $user['password'])) {
            $this->loginUser($user, $remember);
            return true;
        }
        
        return false;
    }
    
    /**
     * Log in user
     */
    public function loginUser(array $user, bool $remember = false): void
    {
        $this->session->regenerate();
        $this->session->set(self::SESSION_KEY, $user['id']);
        $this->user = $user;

        // Set remember token if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie(self::REMEMBER_TOKEN_KEY, $token, time() + (30 * 24 * 60 * 60), '/', '', false, true); // 30 days
            
            $userModel = new User();
            $userModel->updateUser($user['id'], ['remember_token' => $token]);
        }
        
        $logger = new Logger();
        $logger->info("User logged in", ['user_id' => $user['id'], 'email' => $user['email']]);
    }

    /**
     * Login by user ID
     */
    public function loginById(int $userId): bool
    {
        $userRecord = User::find($userId);

        if ($userRecord instanceof User) {
            $user = $userRecord->toArray();
        } elseif (is_array($userRecord)) {
            $user = $userRecord;
        } else {
            return false;
        }

        if (!($user['is_active'] ?? false)) {
            return false;
        }

        $this->loginUser($user);

        return true;
    }
    
    /**
     * Log out user
     */
    public function logout(): void
    {
        if ($this->user) {
            $logger = new Logger();
            $logger->info("User logged out", ['user_id' => $this->user['id']]);
        }
        
        $this->session->forget(self::SESSION_KEY);
        $this->session->regenerate();
        
        // Clear remember token
        if (isset($_COOKIE[self::REMEMBER_TOKEN_KEY])) {
            setcookie(self::REMEMBER_TOKEN_KEY, '', time() - 3600, '/', '', false, true);
        }
        
        $this->user = null;
    }
    
    /**
     * Check if user is authenticated
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }
    
    /**
     * Check if user is guest (not authenticated)
     */
    public function guest(): bool
    {
        return !$this->check();
    }
    
    /**
     * Get authenticated user
     */
    public function user(): ?array
    {
        if ($this->user === null) {
            $userId = $this->session->get(self::SESSION_KEY);

            if ($userId) {
                $record = User::find($userId);

                if ($record instanceof User) {
                    $this->user = $record->toArray();
                } elseif (is_array($record)) {
                    $this->user = $record;
                }
            } elseif (isset($_COOKIE[self::REMEMBER_TOKEN_KEY])) {
                $token = $_COOKIE[self::REMEMBER_TOKEN_KEY];
                $user = DB::fetch(
                    'SELECT * FROM users WHERE remember_token = ? AND is_active = 1 LIMIT 1',
                    [$token]
                );

                if ($user) {
                    $this->loginUser($user, true);
                }
            }
        }

        return $this->user;
    }
    
    /**
     * Get authenticated user ID
     */
    public function id(): ?int
    {
        $user = $this->user();
        return $user ? $user['id'] : null;
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        $user = $this->user();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Check if user is realtor
     */
    public function isRealtor(): bool
    {
        return $this->hasRole('realtor');
    }
    
    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->hasRole('tenant');
    }
    
    /**
     * Check if user can perform action (basic permission check)
     */
    public function can(string $permission): bool
    {
        $user = $this->user();
        
        if (!$user) {
            return false;
        }
        
        // Admin can do everything
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Define role-based permissions
        $permissions = [
            'realtor' => [
                'properties.create',
                'properties.update',
                'properties.delete',
                'posts.create',
                'posts.update',
                'posts.delete',
                'exports.create',
                'analytics.view'
            ],
            'tenant' => [
                'favorites.manage',
                'orders.create',
                'profile.update'
            ]
        ];
        
        $rolePermissions = $permissions[$user['role']] ?? [];
        return in_array($permission, $rolePermissions);
    }
    
    /**
     * Change user password
     */
    public function changePassword(string $currentPassword, string $newPassword): bool
    {
        $user = $this->user();
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }
        
        $userModel = new User();
        $success = $userModel->updateUser($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        if ($success) {
            $logger = new Logger();
            $logger->info("Password changed", ['user_id' => $user['id']]);
        }
        
        return $success;
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $userModel = new User();
        $user = $userModel->findByPasswordResetToken($token);
        
        if (!$user || !$user['password_reset_expires'] || 
            strtotime($user['password_reset_expires']) < time()) {
            return false;
        }
        
        $success = $userModel->updateUser($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
        
        if ($success) {
            $logger = new Logger();
            $logger->info("Password reset", ['user_id' => $user['id'], 'email' => $user['email']]);
        }
        
        return $success;
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken(string $email): ?string
    {
        if (!User::findByEmail($email)) {
            return null;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
        
        // Store token (in a real app, you'd have a password_resets table)
        $this->session->set("reset_token_{$token}", [
            'email' => $email,
            'expires_at' => $expiry
        ]);
        
        return $token;
    }
    
    /**
     * Verify reset token
     */
    public function verifyResetToken(string $token): ?string
    {
        $tokenData = $this->session->get("reset_token_{$token}");
        
        if (!$tokenData || strtotime($tokenData['expires_at']) < time()) {
            return null;
        }
        
        return $tokenData['email'];
    }
    
    /**
     * Clear reset token
     */
    public function clearResetToken(string $token): void
    {
        $this->session->forget("reset_token_{$token}");
    }
    
    /**
     * Middleware for authentication
     */
    public static function middleware(): callable
    {
        return function (Request $request, callable $next) {
            $auth = new self();
            
            if (!$auth->check()) {
                if ($request->expectsJson()) {
                    return Response::json(['error' => 'Unauthorized'], 401);
                }
                
                return Response::redirect('/login');
            }
            
            return $next($request);
        };
    }
    
    /**
     * Middleware for role-based access
     */
    public static function roleMiddleware(string $role): callable
    {
        return function (Request $request, callable $next) use ($role) {
            $auth = new self();
            
            if (!$auth->check() || !$auth->hasRole($role)) {
                if ($request->expectsJson()) {
                    return Response::json(['error' => 'Forbidden'], 403);
                }
                
                return Response::redirect('/');
            }
            
            return $next($request);
        };
    }
}
