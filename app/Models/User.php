<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'cpf', 
        'phone', 'zip_code', 'address', 'number', 'neighborhood', 'city', 'state',
        'document_path', 'rg', 'admission_date', 'complement', 'birth_date', 'gender'
    ];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'admission_date' => 'date',
        'birth_date' => 'date',
    ];

    /**
     * Relacionamento com Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

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
        if (!$this->role) {
            return false;
        }
        
        return $this->normalizeRole($this->role->name) === $this->normalizeRole($role);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    protected $permissionsCache = null;

    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->permissionsCache === null) {
            // Direct user permissions
            $userPerms = $this->permissions()->pluck('name')->toArray();
            
            // Role permissions
            $rolePerms = [];
            if ($this->role) {
                $rolePerms = $this->role->permissions()->pluck('name')->toArray();
            }
            
            $this->permissionsCache = array_unique(array_merge($userPerms, $rolePerms));
        }

        return in_array($permission, $this->permissionsCache);
    }
}
