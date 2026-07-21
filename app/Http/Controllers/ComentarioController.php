<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function marcarLeidos(Request $request)
    {
        $request->validate([
            'comentable_type' => 'required|string',
            'comentable_id' => 'required|integer',
            'tipos' => 'required|array',
        ]);

        // Marcar como leídos los comentarios del usuario actual
        $actualizados = Comentario::where('comentable_type', $request->comentable_type)
            ->where('comentable_id', $request->comentable_id)
            ->whereIn('tipo', $request->tipos)
            ->where('leido', false)
            ->update([
                'leido' => true,
                'leido_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'marcados' => $actualizados,
            'message' => "Se marcaron {$actualizados} comentarios como leídos."
        ]);
    }
}