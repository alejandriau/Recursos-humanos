<?php
// app/Exports/PasivoUnoExport.php

namespace App\Exports;

use App\Models\PasivoUno;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class PasivoUnoExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        // Obtener todas las letras del abecedario
        $letras = range('A', 'Z');

        foreach ($letras as $letra) {
            $sheets[] = new PasivoUnoPorLetraSheet($letra);
        }

        return $sheets;
    }
}
