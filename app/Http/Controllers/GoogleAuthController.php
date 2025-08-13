<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite as FacadesSocialite; 

class GoogleAuthController extends Controller
{
    // Redirecciona a la página de autenticación de Google
    public function redirect(): RedirectResponse
    {
        return FacadesSocialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google y autentica al usuario
    public function callback(): RedirectResponse
    {
        $googleUser = FacadesSocialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // Si el usuario no existe, se crea un nuevo usuario
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuario',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(32)),
            ]);
        } else {
            // Si el usuario existe, se actualiza el avatar si es necesario
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->avatar = $googleUser->getAvatar();
                $user->save();
            }
        }

        // Se autentica al usuario
        Auth::login($user, true);

        // Se redirige a la página principal
        return redirect()->intended('/dashboard');
    }
}