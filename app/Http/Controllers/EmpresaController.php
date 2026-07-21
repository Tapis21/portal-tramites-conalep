<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::vigentes(); // Solo empresas activas con convenio vigente

        $filtro = $request->get('filtro', 'todos');

        if ($filtro === 'ss') {
            $query->conServicioSocial();
        } elseif ($filtro === 'pp') {
            $query->conPracticas();
        } elseif ($filtro === 'ambos') {
            $query->conServicioSocial()->conPracticas();
        }

        $empresas = $query->orderBy('nombre')->get();

        return view('empresas.index', compact('empresas', 'filtro'));
    }
}