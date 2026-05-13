<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $employees = User::select('id', 'name', 'role', 'email', 'cpf', 'document_path')
            ->orderBy('name')
            ->get();
        return view('users.index', compact('employees'));
    }

    public function create()
    {
        return redirect()->route('register');
    }

    public function edit(User $user)
    {
        $roles = \Illuminate\Support\Facades\Cache::remember('roles_list', 300, function () {
            return Role::select('id', 'name', 'description')
                ->orderBy('name')
                ->get();
        });

        $permissions = \Illuminate\Support\Facades\Cache::remember('permissions_by_group', 300, function () {
            return Permission::select('id', 'name', 'label', 'group')
                ->orderBy('group')
                ->orderBy('label')
                ->get()
                ->groupBy('group');
        });
        $userPermissions = $user->permissions->pluck('id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'role'         => 'required|string|max:255|exists:roles,name',
            'cpf'          => 'required|string|max:14|unique:users,cpf,' . $user->id,
            'rg'           => 'required|string|max:12',
            'phone'        => 'required|string|max:20',
            'zip_code'     => 'required|string|max:10',
            'address'      => 'required|string|max:255',
            'number'       => 'required|string|max:20',
            'neighborhood' => 'required|string|max:100',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:2',
            'password'     => 'nullable|min:8|confirmed',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        
        // Sync permissions
        $user->permissions()->sync($request->permissions ?? []);

        Logger::log('update_user', "O usuário alterou os dados e permissões do funcionário: {$user->name} (#{$user->id})");

        return redirect()->route('employees.index')->with('success', 'Dados do funcionário atualizados com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['Não é possível excluir seu próprio usuário.']);
        }

        $userName = $user->name;
        $userId = $user->id;
        $user->delete();

        Logger::log('delete_user', "O usuário removeu o funcionário: {$userName} (#{$userId})");

        return redirect()->route('employees.index')->with('success', 'Funcionário removido com sucesso!');
    }
}
