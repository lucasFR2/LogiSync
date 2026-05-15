<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
            $this->permissionsCache = $this->permissions()->pluck('name')->toArray();
        }

        return in_array($permission, $this->permissionsCache);
    }
}
