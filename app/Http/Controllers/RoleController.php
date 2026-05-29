<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Helpers\Logger;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::select('id', 'name', 'description')
            ->with(['permissions:id,label,group'])
            ->orderBy('name')
            ->get();

        $permissions = Permission::select('id', 'name', 'label', 'group')
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::select('id', 'name', 'label', 'group')
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:roles,name',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Logger::log('create_role', "O usuário criou o cargo: {$role->name} com " . count($validated['permissions'] ?? []) . " permissões.");

        return redirect()->route('roles.index')->with('success', 'Cargo criado com sucesso!');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::select('id', 'name', 'label', 'group')
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        Logger::log('update_role', "O usuário alterou o cargo: {$role->name} e suas permissões.");

        return redirect()->route('roles.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    public function destroy(Role $role)
    {
        $roleName = $role->name;
        $role->delete();
        Logger::log('delete_role', "O usuário removeu o cargo: {$roleName}");

        return redirect()->route('roles.index')->with('success', 'Cargo removido com sucesso!');
    }
}
