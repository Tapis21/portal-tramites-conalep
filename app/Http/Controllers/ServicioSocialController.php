<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioSocialController extends Controller
{
    // Muestra el progreso del SS del estudiante autenticado
    public function index()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        return view('servicio_social.index', compact('servicioSocial'));
    }

    // Formulario para actualizar horas (solo para pruebas)
    public function edit($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.edit', compact('servicioSocial'));
    }

    // Actualiza las horas completadas
    public function update(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'horas_completadas' => 'required|integer|min:0|max:480',
        ]);

        $servicioSocial->update([
            'horas_completadas' => $request->horas_completadas
        ]);

        return redirect()->route('servicio-social.index')
                         ->with('success', 'Horas actualizadas correctamente.');
    }
}