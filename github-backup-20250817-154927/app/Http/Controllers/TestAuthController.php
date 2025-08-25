<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TestAuthController extends Controller
{
    public function testAuth(): View
    {
        $user = Auth::user();
        $isAuthenticated = Auth::check();
        
        return view('test-auth', compact('user', 'isAuthenticated'));
    }
}
