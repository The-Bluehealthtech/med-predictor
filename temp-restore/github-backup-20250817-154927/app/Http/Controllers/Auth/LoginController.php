<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function showLoginForm()
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Log successful login
            AuditTrailService::logLogin(Auth::user(), true);

            $user = Auth::user();
            
            // Si l'utilisateur est un joueur, rediriger vers le portail joueur FIFA Ultimate
            if ($user->role === 'player' && $user->player) {
                return redirect()->intended(route('player-portal.fifa-ultimate'));
            }
            
            // Sinon, rediriger vers le dashboard principal
            return redirect()->intended(route('dashboard'));
        }

        // Log failed login attempt
        AuditTrailService::logFailedLogin($request->email, 'Invalid credentials');

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        // Log logout before destroying session
        if (Auth::check()) {
            AuditTrailService::logLogout(Auth::user());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
} 