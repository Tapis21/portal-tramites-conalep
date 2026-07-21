<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Si el usuario ya está autenticado, redirigir según su rol
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('filament.admin.pages.dashboard');
            }
            return redirect()->route('dashboard');
        }

        // Si no está autenticado, mostrar la landing page
        return view('home');
    }
}