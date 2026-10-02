<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Joueur : son portail ; arbitre : son espace. Tout le staff arrive sur le
        // tableau de bord général (les anciens tableaux de bord restent accessibles
        // par leurs adresses ; /modules est à un clic).
        if ($user->isPlayer()) {
            return redirect()->route('test.portail.joueur.simple');
        }
        if ($user->isReferee() && \Illuminate\Support\Facades\Route::has('referee.dashboard')) {
            return redirect()->route('referee.dashboard');
        }

        return view('dashboard.general', [
            'board' => app(\App\Services\Dashboard\GeneralDashboard::class)->forUser($user),
        ]);
    }
} 