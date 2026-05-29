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
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'role_id', 'email', 'cpf', 'document_path')
            ->with('role:id,name');
        
        // General search
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        // Specific Role Filter
        if ($roleFilter = $request->get('role_filter')) {
            $query->whereHas('role', function($q) use ($roleFilter) {
                $q->where('id', $roleFilter);
            });
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();
        
        // Fetch roles for the filter dropdown
        $roles = Role::orderBy('name')->get();
        
        return view('users.index', compact('employees', 'roles'));
    }

    public function create()
    {
        return redirect()->route('register');
    }

    public function edit(User $user)
    {
        $roles = Role::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        $permissions = Permission::select('id', 'name', 'label', 'group')
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
        $userPermissions = $user->permissions->pluck('id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'role_id'      => 'required|integer|exists:roles,id',
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
