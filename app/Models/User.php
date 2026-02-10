<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permissionSlug)
    {
        // Super Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function isCashier()
    {
        return $this->hasRole('cashier');
    }

    /**
     * Check if user can perform an action (permission check with fallback to role check)
     * Compatible with Laravel's authorization system
     */
    public function can($abilities, $arguments = [])
    {
        // If it's a string (permission slug), check permission
        if (is_string($abilities)) {
            return $this->hasPermission($abilities);
        }
        
        // Fallback to parent implementation for other authorization checks
        return parent::can($abilities, $arguments);
    }
}
