<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsPersonalExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $analyticsData;

    public function __construct($analyticsData)
    {
        $this->analyticsData = $analyticsData;
    }

    public function collection()
    {
        return collect($this->analyticsData);
    }

    public function headings(): array
    {
        return [
            'Métrica',
            'Valor',
            'Tendencia',
            'Comparación Mes Anterior',
            'Objetivo',
            'Estado',
            'Recomendación'
        ];
    }

    public function map($data): array
    {
        return [
            $data['metrica'],
            $data['valor'],
            $data['tendencia'],
            $data['comparacion'],
            $data['objetivo'],
            $this->getEstado($data['valor'], $data['objetivo']),
            $data['recomendacion']
        ];
    }

    private function getEstado($valor, $objetivo)
    {
        if ($valor >= $objetivo * 1.1) return '✅ Excelente';
        if ($valor >= $objetivo) return '🟢 Bueno';
        if ($valor >= $objetivo * 0.8) return '🟡 Regular';
        return '🔒 Necesita mejora';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']]
            ]
        ];
    }
}
