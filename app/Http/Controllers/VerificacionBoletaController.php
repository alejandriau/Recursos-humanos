<?php

namespace App\Http\Controllers;

use App\Models\Salida;

class VerificacionBoletaController extends Controller
{
    public function verificar($id)
    {
        // Si la URL no tiene una firma válida, el middleware 'signed'
        // ya rechazó la petición antes de llegar aquí (403).
        $salida = Salida::with('persona')->find($id);

        if (!$salida) {
            return view('verificacion', ['valida' => false]);
        }

        return view('verificacion', [
            'valida'   => true,
            'codigo'   => $salida->codigo,
            'nombre'   => "{$salida->persona->nombre} {$salida->persona->apellidoPat}",
            'fechasal' => \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y'),
            'fecharet' => \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y'),
            'estado'   => $salida->estado,
        ]);
    }
}
