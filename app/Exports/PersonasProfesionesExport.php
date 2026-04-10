<?php

namespace App\Exports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PersonasProfesionesExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Persona::query();

        $query->with([
            'profesiones' => function ($q) {
                $q->where('estado', 1);
            },
            'profesiones.carrera.areaConocimiento',
            'profesiones.carrera.nivelAcademico',
            'profesiones.nivelEstudiado'
        ]);

        // Búsqueda
        if ($this->request->filled('buscar')) {
            $buscar = $this->request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('apellidoPat', 'like', "%$buscar%")
                  ->orWhere('apellidoMat', 'like', "%$buscar%")
                  ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$buscar%"])
                  ->orWhereHas('profesiones', function ($q2) use ($buscar) {
                      $q2->where('provisionN', 'like', "%$buscar%")
                         ->orWhere('diploma', 'like', "%$buscar%")
                         ->orWhereHas('carrera', function ($q3) use ($buscar) {
                             $q3->where('nombre', 'like', "%$buscar%");
                         });
                  });
            });
        }

        // Filtro por área de conocimiento
        if ($this->request->filled('idAreaConocimiento')) {
            $query->whereHas('profesiones.carrera', function ($q) {
                $q->where('idAreaConocimiento', $this->request->idAreaConocimiento);
            });
        }

        // Filtro por nivel académico
        if ($this->request->filled('idNivelAcademico')) {
            $query->whereHas('profesiones.carrera', function ($q) {
                $q->where('idNivelAcademico', $this->request->idNivelAcademico);
            });
        }

        // Filtro por profesión principal
        if ($this->request->filled('esPrincipal')) {
            $query->whereHas('profesiones', function ($q) {
                $q->where('esPrincipal', $this->request->esPrincipal);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombres',
            'Apellido Paterno',
            'Apellido Materno',
            'ID Prof.',
            'Carrera',
            'Área Conoc.',
            'Nivel Acad.',
            'Nivel Est.',
            'Estado Estudio',
            'Diploma',
            'Fecha Título',
            'N° Provisión',
            'Fecha Provisión',
            'Universidad',
            'Registro',
            'Observación',
            'Principal',
            'PDF Diploma',
            'PDF Provisión',
            'PDF Cédula'
        ];
    }

    public function map($persona): array
    {
        $rows = [];

        if ($persona->profesiones->isEmpty()) {
            return [
                $persona->id,
                $persona->nombre,
                $persona->apellidoPat,
                $persona->apellidoMat,
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
            ];
        }

        foreach ($persona->profesiones as $profesion) {
            $rows[] = [
                $persona->id,
                $persona->nombre,
                $persona->apellidoPat,
                $persona->apellidoMat,
                $profesion->id,
                $profesion->carrera->nombre ?? '',
                $profesion->carrera->areaConocimiento->nombre ?? '',
                $profesion->carrera->nivelAcademico->nombre ?? '',
                $profesion->nivelEstudiado->nombre ?? '',
                $this->formatEstadoEstudio($profesion->estadoEstudio),
                $profesion->diploma,
                $profesion->fechaTitulo ? Carbon::parse($profesion->fechaTitulo)->format('d/m/Y') : '',
                $profesion->provisionN,
                $profesion->fechaProvision ? Carbon::parse($profesion->fechaProvision)->format('d/m/Y') : '',
                $profesion->universidad,
                $profesion->registro,
                $profesion->observacion,
                $profesion->esPrincipal ? '✓ Sí' : '✗ No',
                $profesion->pdfDiploma ? url('storage/' . $profesion->pdfDiploma) : '',
                $profesion->pdfProvision ? url('storage/' . $profesion->pdfProvision) : '',
                $profesion->pdfcedulap ? url('storage/' . $profesion->pdfcedulap) : '',
            ];
        }

        return $rows;
    }

    private function formatEstadoEstudio($estado)
    {
        $estados = [
            'en_curso' => 'En curso',
            'incompleto' => 'Incompleto',
            'egresado' => 'Egresado',
            'titulado' => 'Titulado'
        ];
        return $estados[$estado] ?? $estado;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalRows = $sheet->getHighestRow();
                $totalCols = $sheet->getHighestColumn();

                // === 1. ESTILO ENCABEZADOS ===
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
                        'name' => 'Calibri'
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ],
                ];
                $sheet->getStyle('A1:' . $totalCols . '1')->applyFromArray($headerStyle);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // === 2. BORDES Y WRAP PARA DATOS ===
                if ($totalRows > 1) {
                    $dataStyle = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'B0B0B0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ];
                    $sheet->getStyle('A2:' . $totalCols . $totalRows)->applyFromArray($dataStyle);
                }

                // === 3. ALINEACIONES ESPECÍFICAS ===
                $sheet->getStyle('A2:A' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E2:E' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L2:L' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N2:N' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('R2:R' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('Q2:Q' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('S2:U' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // === 4. FILAS ZEBRA ===
                if ($totalRows > 1) {
                    for ($row = 2; $row <= $totalRows; $row++) {
                        if ($row % 2 == 0) {
                            $sheet->getStyle('A' . $row . ':' . $totalCols . $row)
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('E6F0FA');
                        } else {
                            $sheet->getStyle('A' . $row . ':' . $totalCols . $row)
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('FFFFFF');
                        }
                    }
                }

                // === 5. AJUSTE AUTOMÁTICO DE ANCHO DE COLUMNAS (MEJORADO) ===
                $columnLetters = range('A', $totalCols);
                foreach ($columnLetters as $col) {
                    $maxLength = 0;
                    // Recorrer todas las filas para obtener el texto más largo de la columna
                    for ($row = 1; $row <= $totalRows; $row++) {
                        $cellValue = $sheet->getCell($col . $row)->getCalculatedValue();
                        if ($cellValue) {
                            $length = mb_strlen($cellValue, 'UTF-8');
                            if ($length > $maxLength) {
                                $maxLength = $length;
                            }
                        }
                    }
                    // Calcular ancho aproximado en puntos (aproximación: 1.2 por carácter)
                    $width = $maxLength * 1.2;
                    // Limitar ancho mínimo y máximo
                    $minWidth = 8;
                    $maxWidth = 60; // Para columnas de URLs, limitamos a 60
                    if ($width < $minWidth) $width = $minWidth;
                    if ($width > $maxWidth) $width = $maxWidth;
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // === 6. CONGELAR ENCABEZADO ===
                $sheet->freezePane('A2');

                // === 7. FILTROS AUTOMÁTICOS ===
                $sheet->setAutoFilter('A1:' . $totalCols . '1');

                // === 8. AJUSTE AUTOMÁTICO DE ALTURA DE FILAS ===
                if ($totalRows > 1) {
                    for ($row = 2; $row <= $totalRows; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(-1);
                    }
                }
            },
        ];
    }
}