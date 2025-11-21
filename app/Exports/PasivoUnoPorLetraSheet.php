<?php
// app/Exports/PasivoUnoPorLetraSheet.php

namespace App\Exports;

use App\Models\PasivoUno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PasivoUnoPorLetraSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $letra;

    public function __construct($letra)
    {
        $this->letra = $letra;
    }

    public function collection()
    {
        $datos = PasivoUno::where('estado', 1)
            ->whereRaw('UPPER(SUBSTRING(TRIM(nombrecompleto), 1, 1)) = ?', [$this->letra])
            ->orderBy('nombrecompleto')
            ->get();

        return $datos->map(function ($item, $index) {
            return [
                'Nº' => $index + 1,
                'Código' => $item->codigo,
                'Nombre Completo' => $item->nombrecompleto,
                'Observación' => $item->observacion,
                'Letra Inicial' => strtoupper(substr(trim($item->nombrecompleto), 0, 1))
            ];
        });
    }

    public function title(): string
    {
        return "Letra {$this->letra}";
    }

    public function headings(): array
    {
        return [
            'Nº',
            'Código',
            'Nombre Completo',
            'Observación',
            'Letra Inicial'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:E' => ['font' => ['size' => 10]],
        ];
    }
}
