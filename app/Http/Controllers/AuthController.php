<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Helpers\Logger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function formatCpf(string $digits): string
    {
        $digits = preg_replace('/\D/', '', $digits) ?? '';
        $digits = substr($digits, 0, 11);

        return substr($digits, 0, 3)
            . '.' . substr($digits, 3, 3)
            . '.' . substr($digits, 6, 3)
            . '-' . substr($digits, 9, 2);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        $roles = \Illuminate\Support\Facades\Cache::remember('roles_list', 300, function () {
            return Role::select('id', 'name', 'description')
                ->orderBy('name')
                ->get();
        });
        return view('auth.register', compact('roles'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Credenciais inválidas.']);
        }

        Logger::log('login', 'O usuário realizou login no sistema.');

        return redirect()->route('dashboard');
    }

    public function register(Request $request)
    {
        $rawCpf = (string) $request->input('cpf', '');
        $cpfDigits = preg_replace('/\D/', '', $rawCpf) ?? '';
        if (strlen($cpfDigits) === 11) {
            $request->merge(['cpf' => $this->formatCpf($cpfDigits)]);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'role'         => 'required|string|max:255|exists:roles,name',
            'cpf'          => ['required', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', 'unique:users,cpf'],
            'password'     => 'required|min:8|confirmed',
            'phone'        => 'nullable|string|max:20',
            'zip_code'     => 'nullable|string|max:10',
            'address'      => 'nullable|string|max:255',
            'number'       => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:2',
            'documents'    => 'required|array',
            'documents.*'  => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'rg'           => 'required|string|max:20',
        ]);

        $cpfDigits = preg_replace('/\D/', '', (string) $data['cpf']) ?? '';
        if (strlen($cpfDigits) !== 11) {
            return back()->withErrors(['cpf' => 'CPF inválido.'])->withInput();
        }

        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documentPaths[] = $file->store('documents/users', 'public');
            }
        }

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'role'         => $data['role'],
            'cpf'          => $this->formatCpf($cpfDigits),
            'rg'           => $data['rg'],
            'phone'        => $data['phone'],
            'zip_code'     => $data['zip_code'],
            'address'      => $data['address'],
            'number'       => $data['number'],
            'neighborhood' => $data['neighborhood'],
            'city'         => $data['city'],
            'state'        => $data['state'],
            'document_path'=> json_encode($documentPaths),
        ]);

        if (Auth::check()) {
            Logger::log('register_user', "O usuário cadastrou um novo funcionário: {$user->name} ({$user->role})");
            return back()->with('success', 'Funcionário cadastrado com sucesso!');
        }

        Auth::login($user);
        Logger::log('self_register', 'O usuário realizou seu próprio cadastro e login.');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Logger::log('logout', 'O usuário saiu do sistema.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
