<?php
// app/Exports/PasivoUnoPorLetraSheet.php

namespace App\Exports;

use App\Models\PasivoUno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PasivoUnoPorLetraSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithEvents
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
        return "LETRA {$this->letra}";
    }

    public function headings(): array
    {
        return [
            ['GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA'],
            ['Unidad de Gestión de Recursos Humanos - UGRH'],
            ['REPORTE PASIVO UNO - EX CORDECO'],
            ["LETRA: {$this->letra}"],
            [], // Línea en blanco
            ['Nº', 'CÓDIGO', 'NOMBRE COMPLETO', 'OBSERVACIONES', 'LETRA INICIAL']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para los títulos
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->mergeCells('A4:E4');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2c5aa0']],
                'alignment' => ['horizontal' => 'center']
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center']
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '2c5aa0']],
                'alignment' => ['horizontal' => 'center']
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => 'center']
            ],
            6 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2c5aa0']],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ],
            'A:E' => ['font' => ['size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Obtener la hoja
                $sheet = $event->sheet->getDelegate();

                // Autoajustar columnas
                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Agregar información al final
                $lastRow = $sheet->getHighestRow() + 2;
                $sheet->setCellValue("A{$lastRow}", "Generado el: " . date('d/m/Y H:i:s'));
                $sheet->setCellValue("A" . ($lastRow + 1), "Sistema: SIGRH GADC - UGRH");
                $sheet->setCellValue("A" . ($lastRow + 2), "Usuario: Sistema UGRH");
            },
        ];
    }
}
