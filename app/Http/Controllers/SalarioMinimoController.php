<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSalarioMinimo;
use Illuminate\Http\Request;

class SalarioMinimoController extends Controller
{
    public function index()
    {
        $salarios = ConfiguracionSalarioMinimo::orderBy('gestion', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $salarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion' => 'required|integer',
            'monto_salario_minimo' => 'required|numeric|min:0',
            'fecha_vigencia' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        // Desactivar otros salarios vigentes
        if ($request->vigente) {
            ConfiguracionSalarioMinimo::where('vigente', true)->update(['vigente' => false]);
        }

        $salario = ConfiguracionSalarioMinimo::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Salario mínimo registrado exitosamente',
            'data' => $salario
        ], 201);
    }

    public function obtenerVigente()
    {
        $salarioVigente = ConfiguracionSalarioMinimo::obtenerSalarioVigente();

        return response()->json([
            'success' => true,
            'data' => $salarioVigente
        ]);
    }
}
