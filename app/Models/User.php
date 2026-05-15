<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'cpf', 
        'phone', 'zip_code', 'address', 'number', 'neighborhood', 'city', 'state',
        'document_path', 'rg'
    ];
    protected $hidden = ['password', 'remember_token'];

    private function normalizeRole(?string $role): string
    {
        $role = trim(mb_strtolower($role ?? ''));

        return match ($role) {
            'administrador', 'admin' => 'admin',
            'recursos humanos (rh)', 'recursos humanos', 'rh' => 'rh',
            default => $role,
        };
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasRole(string $role): bool
    {
        return $this->normalizeRole($this->role) === $this->normalizeRole($role);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    protected $permissionsCache = null;

    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->permissionsCache === null) {
            // Direct user permissions
            $userPerms = $this->permissions()->pluck('name')->toArray();
            
            // Role permissions
            $rolePerms = [];
            $roleRecord = Role::where('name', $this->role)->first();
            if ($roleRecord) {
                $rolePerms = $roleRecord->permissions()->pluck('name')->toArray();
            }
            
            $this->permissionsCache = array_unique(array_merge($userPerms, $rolePerms));
        }

        return in_array($permission, $this->permissionsCache);
    }
}
