<?php

namespace App\Exports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $personas;

    public function __construct($personas)
    {
        $this->personas = $personas;
    }

    public function collection()
    {
        return $this->personas;
    }

    public function headings(): array
    {
        return [
            'N°',
            'NOMBRE COMPLETO',
            'CÉDULA DE IDENTIDAD',
            'FECHA INGRESO',
            'ANTIGÜEDAD TOTAL',
            'ESTADO CAS',
            'BONO ACTUAL (%)',
            'BONO ACTUAL (BS)',
            'BONO CAS (%)',
            'NIVEL ALERTA',
            'OBSERVACIONES'
        ];
    }

    public function map($persona): array
    {
        $antiguedad = $persona->calculo_antiguedad;
        $bono = $persona->calculo_bono;
        $alerta = $persona->nivel_alerta_persona;

        // Estado CAS
        $estadoCas = 'SIN CAS';
        if ($antiguedad['tiene_cas']) {
            $estadoCas = $antiguedad['cas_vigente'] ? 'CON CAS VIGENTE' : 'CON CAS NO VIGENTE';
        }

        // Bono CAS
        $bonoCas = '-';
        if ($antiguedad['tiene_cas'] && isset($bono['escala_cas'])) {
            $bonoCas = $bono['escala_cas']->porcentaje_bono . '%';
        }

        // Nivel de alerta
        $nivelAlerta = strtoupper($alerta);

        // Observaciones
        $observaciones = '';
        if ($alerta == 'urgente') {
            $observaciones = !$antiguedad['tiene_cas'] ? 'SIN CAS REGISTRADO' : 'NECESITA ACTUALIZAR CAS';
        } elseif ($alerta == 'advertencia') {
            $observaciones = 'CAS NO VIGENTE';
        }

        return [
            $persona->id,
            $persona->nombre . ' ' . $persona->apellidoPat . ' ' . $persona->apellidoMat,
            $persona->ci,
            $persona->fechaIngreso ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : 'NO REGISTRADA',
            $antiguedad['anios'] . ' años ' . $antiguedad['meses'] . ' meses ' . $antiguedad['dias'] . ' días',
            $estadoCas,
            $bono['aplica_bono'] ? $bono['porcentaje'] . '%' : 'NO APLICA',
            $bono['aplica_bono'] ? number_format($bono['monto'], 2) : '0.00',
            $bonoCas,
            $nivelAlerta,
            $observaciones
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el encabezado
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']]
            ],
            // Estilo para las filas con alerta urgente
            'A2:Z1000' => [
                'font' => ['size' => 10]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // N°
            'B' => 30, // NOMBRE COMPLETO
            'C' => 18, // CI
            'D' => 15, // FECHA INGRESO
            'E' => 25, // ANTIGÜEDAD
            'F' => 20, // ESTADO CAS
            'G' => 15, // BONO %
            'H' => 15, // BONO BS
            'I' => 12, // BONO CAS %
            'J' => 15, // NIVEL ALERTA
            'K' => 25, // OBSERVACIONES
        ];
    }
}
