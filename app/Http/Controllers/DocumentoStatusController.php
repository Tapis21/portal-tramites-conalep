<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class DocumentoStatusController extends Controller
{
    public function update(Request $request)
    {
        // Verificar que el usuario es admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'documento_id' => 'required|exists:documentos,id',
            'estatus' => 'required|in:pendiente,validado,validado_ventanilla,rechazado',
            'comentario' => 'nullable|string|max:500',
        ]);

        try {
            $documento = Documento::find($request->documento_id);
            $documento->update([
                'estatus' => $request->estatus,
                'comentario_admin' => $request->comentario ?? $documento->comentario_admin,
            ]);

            // Guardar comentario si existe
            if ($request->comentario) {
                Comentario::create([
                    'contenido' => $request->comentario,
                    'tipo' => 'admin',
                    'user_id' => Auth::id(),
                    'comentable_id' => $documento->id,
                    'comentable_type' => 'App\Models\Documento',
                ]);
            }

            // Notificación de Filament
            $mensajes = [
                'validado' => '✅ Documento validado correctamente.',
                'validado_ventanilla' => '📄 Documento validado en ventanilla.',
                'rechazado' => '❌ Documento rechazado.',
                'pendiente' => '⏳ Estatus del documento restablecido a pendiente.',
            ];

            Notification::make()
                ->title('✅ Estatus actualizado')
                ->body($mensajes[$request->estatus] ?? 'Estatus actualizado correctamente.')
                ->success()
                ->send();

            return redirect()->back()->with('success', 'Estatus actualizado correctamente.');

        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error')
                ->body('Ocurrió un error al actualizar el estatus: ' . $e->getMessage())
                ->danger()
                ->send();

            return redirect()->back()->with('error', 'Error al actualizar el estatus.');
        }
    }
}