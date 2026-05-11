<?php
// app/Exports/PasivoDosExport.php

namespace App\Exports;

use App\Models\Pasivodos;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\PasivoDosPorLetraSheet;

class PasivoDosExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        // Obtener letras únicas de la base de datos
        $letras = Pasivodos::where('estado', 1)
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
