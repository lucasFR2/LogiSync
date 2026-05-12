<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'cpf', 
        'phone', 'zip_code', 'address', 'number', 'neighborhood', 'city', 'state',
        'document_path', 'rg'
    ];
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions
        if ($this->role === 'Administrador') {
            return true;
        }
        return $this->permissions()->where('name', $permission)->exists();
    }
}
