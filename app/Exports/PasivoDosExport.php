<?php
// app/Exports/PasivoDosExport.php

namespace App\Exports;

use App\Models\PasivoDos;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class PasivoDosExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        // Obtener letras únicas de la base de datos
        $letras = PasivoDos::where('estado', 1)
            ->whereNotNull('letra')
            ->where('letra', '!=', '')
            ->distinct()
            ->orderBy('letra')
            ->pluck('letra')
            ->toArray();

        foreach ($letras as $letra) {
            $sheets[] = new PasivoDosPorLetraSheet($letra);
        }

        return $sheets;
    }
}
