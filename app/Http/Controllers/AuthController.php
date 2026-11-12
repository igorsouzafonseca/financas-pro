<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'monthly_budget' => $validated['monthly_budget'] ?? 0.00
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Conta criada com sucesso!', 'user' => $user], 201);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json(['message' => 'Login efetuado com sucesso.', 'user' => auth()->user()]);
    }

    public function logout(Request $request) {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sessão encerrada.']);
    }

    public function me(Request $request) {
        return response()->json(['user' => $request->user()]);
    }

    public function updateProfile(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'role_title' => ['nullable', 'string', 'max:80'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0']
        ]);

        $request->user()->update($validated);

        return response()->json(['message' => 'Perfil atualizado com sucesso.', 'user' => $request->user()]);
    }
}