<?php

namespace App\Http\Controllers;

use App\Models\EscalaBonoAntiguedad;
use Illuminate\Http\Request;

class EscalaBonoController extends Controller
{
    public function index()
    {
        $escalas = EscalaBonoAntiguedad::activas()->get();

        return response()->json([
            'success' => true,
            'data' => $escalas
        ]);
    }

    public function show($id)
    {
        $escala = EscalaBonoAntiguedad::find($id);

        if (!$escala) {
            return response()->json([
                'success' => false,
                'message' => 'Escala no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $escala
        ]);
    }
}
