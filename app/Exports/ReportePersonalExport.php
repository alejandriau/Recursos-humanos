<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportePersonalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $personas;
    protected $tipoReporte;

    public function __construct($personas, $tipoReporte = 'censo')
    {
        $this->personas = $personas;
        $this->tipoReporte = $tipoReporte;
    }

    public function collection()
    {
        return $this->personas;
    }

    public function headings(): array
    {
        switch ($this->tipoReporte) {
            case 'censo':
                return [
                    'CI',
                    'Nombre Completo',
                    'Sexo',
                    'Edad',
                    'Fecha Ingreso',
                    'Antigüedad (Años)',
                    'Puesto',
                    'Unidad Organizacional',
                    'Nivel Jerárquico',
                    'Teléfono',
                    'Estado'
                ];
            case 'documentacion':
                return [
                    'CI',
                    'Nombre Completo',
                    'Puesto',
                    'Unidad',
                    'Diploma',
                    'Provisión',
                    'Cédula Profesional',
                    'Estado Documentación',
                    'Documentos Faltantes'
                ];
            default:
                return [
                    'CI',
                    'Nombre Completo',
                    'Puesto',
                    'Unidad',
                    'Estado'
                ];
        }
    }

public function map($persona): array
{
    $puestoActual = $persona->historialActivo->puesto ?? null;
    $unidadActual = $puestoActual->unidadOrganizacional ?? null;

    switch ($this->tipoReporte) {
        case 'censo':
            return [
                $persona->ci,
                $persona->nombre_completo,
                $persona->sexo,
                $persona->edad,
                $persona->fechaIngreso ? $persona->fechaIngreso->format('d/m/Y') : '',
                $persona->antiguedad,
                $puestoActual ? $puestoActual->denominacion : 'Sin puesto',
                $unidadActual ? $unidadActual->denominacion : 'Sin unidad',
                $puestoActual ? $puestoActual->nivelJerarquico : '',
                $persona->telefono ?: 'N/A',
                $persona->estado ? 'Activo' : 'Inactivo'
            ];
        case 'documentacion':
            $tieneDiploma = $persona->profesiones->whereNotNull('pdfDiploma')->isNotEmpty();
            $tieneProvision = $persona->profesiones->whereNotNull('pdfProvision')->isNotEmpty();
            $tieneCedula = $persona->profesiones->whereNotNull('pdfcedulap')->isNotEmpty();

            $documentacionCompleta = $tieneDiploma && $tieneProvision && $tieneCedula;

            $faltantes = [];
            if (!$tieneDiploma) $faltantes[] = 'Diploma';
            if (!$tieneProvision) $faltantes[] = 'Provision';
            if (!$tieneCedula) $faltantes[] = 'Cédula';

            return [
                $persona->ci,
                $persona->nombre_completo,
                $puestoActual ? $puestoActual->denominacion : 'Sin puesto',
                $unidadActual ? $unidadActual->denominacion : 'Sin unidad',
                $tieneDiploma ? 'SI' : 'NO',
                $tieneProvision ? 'SI' : 'NO',
                $tieneCedula ? 'SI' : 'NO',
                $documentacionCompleta ? 'COMPLETA' : 'INCOMPLETA',
                implode(', ', $faltantes)
            ];
        default:
            return [
                $persona->ci,
                $persona->nombre_completo,
                $puestoActual ? $puestoActual->denominacion : 'Sin puesto',
                $unidadActual ? $unidadActual->denominacion : 'Sin unidad',
                $persona->estado ? 'Activo' : 'Inactivo'
            ];
    }
}
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el encabezado
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']]
            ],
            // Autoajustar columnas
            'A:Z' => [
                'alignment' => ['vertical' => 'center']
            ]
        ];
    }

    public function title(): string
    {
        switch ($this->tipoReporte) {
            case 'censo': return 'Censo Laboral';
            case 'documentacion': return 'Estado Documentación';
            default: return 'Reporte Personal';
        }
    }
}
