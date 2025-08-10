<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método para mostrar el formulario de inicio de sesión
    public function showLogin()
    {
        return view('auth.login');
    }

    // Método para mostrar el formulario de registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Método para manejar el inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Usuario o contraseña incorrectos',
        ])->withInput($request->only('email'));
    }

    // Método para manejar el registro de usuarios
    public function register(Request $request)
    {
        \Log::info('Registro intentado', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            \Log::error('Validación falló', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            
            \Log::info('Usuario creado', ['id' => $user->id, 'email' => $user->email]);
            
            Auth::login($user);
            return redirect('/dashboard');
        } catch (\Exception $e) {
            \Log::error('Error creando usuario', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al crear usuario'])->withInput();
        }
    }

    // Método para cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}