<?php

namespace App\Http\Controllers;

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
        return view('auth.register');
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'role'     => 'required|string|max:255',
            'cpf'      => ['required', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', 'unique:users,cpf'],
            'password' => 'required|min:8|confirmed',
        ]);

        $cpfDigits = preg_replace('/\D/', '', (string) $data['cpf']) ?? '';
        if (strlen($cpfDigits) !== 11) {
            return back()->withErrors(['cpf' => 'CPF inválido.'])->withInput();
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'cpf'      => $this->formatCpf($cpfDigits),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
