<?php

namespace App\Exports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportePersonalizadoExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $columnas;
    protected $filtros;
    protected $nombresColumnas;

    public function __construct(array $columnas, array $filtros)
    {
        $this->columnas = $columnas;
        $this->filtros = $filtros;
        $this->nombresColumnas = $this->getNombresColumnas();
    }

    public function collection()
    {
        // Obtener datos directamente sin depender del controlador
        $personas = $this->obtenerPersonasFiltradas();

        // Filtrar solo las columnas seleccionadas
        return $personas->map(function($persona) {
            $row = [];
            foreach ($this->columnas as $columna) {
                $row[$columna] = $persona[$columna] ?? '';
            }
            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->columnas as $columna) {
            $headings[] = $this->nombresColumnas[$columna] ?? $columna;
        }

        return $headings;
    }

    private function obtenerPersonasFiltradas()
    {
        // Simular un request para usar la misma lógica del controlador
        $request = new Request($this->filtros);

        $query = Persona::where('persona.estado', 1)
            ->select('persona.*')
            ->with([
                'historialActivo.puesto.unidadOrganizacional',
                'casActual',
                'profesiones' => function($q) {
                    $q->where('estado', 1);
                },
                'certificados' => function($q) {
                    $q->where('estado', 1);
                },
                'licenciaMilitar' => function($q) {
                    $q->where('estado', 1);
                }
            ]);

        // Aplicar filtros
        if (isset($this->filtros['unidad_id'])) {
            $query->whereHas('historialActivo.puesto.unidadOrganizacional', function($q) {
                $q->where('id', $this->filtros['unidad_id']);
            });
        }

        if (isset($this->filtros['tipo_contrato'])) {
            $query->whereHas('historialActivo.puesto', function($q) {
                $q->where('tipoContrato', $this->filtros['tipo_contrato']);
            });
        }

        if (isset($this->filtros['nivel_jerarquico'])) {
            $query->whereHas('historialActivo.puesto', function($q) {
                $q->where('nivelJerarquico', $this->filtros['nivel_jerarquico']);
            });
        }

        if (isset($this->filtros['es_jefatura'])) {
            $query->whereHas('historialActivo.puesto', function($q) {
                $q->where('esJefatura', $this->filtros['es_jefatura'] == 'si');
            });
        }

        if (isset($this->filtros['sexo'])) {
            $query->where('sexo', $this->filtros['sexo']);
        }

        if (isset($this->filtros['estado_cas'])) {
            $query->whereHas('casActual', function($q) {
                $q->where('estado_cas', $this->filtros['estado_cas']);
            });
        }

        if (isset($this->filtros['busqueda'])) {
            $busqueda = $this->filtros['busqueda'];
            $query->where(function($q) use ($busqueda) {
                $q->where('ci', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$busqueda}%");
            });
        }

        // Ordenar
        if (isset($this->filtros['ordenar_por'])) {
            $orden = $this->filtros['ordenar_direccion'] ?? 'asc';
            $query->orderBy($this->filtros['ordenar_por'], $orden);
        } else {
            $query->orderBy('apellidoPat')->orderBy('nombre');
        }

        // Límite de registros
        if (isset($this->filtros['limite']) && $this->filtros['limite'] > 0) {
            $query->limit($this->filtros['limite']);
        }

        return $query->get()->map(function($persona) {
            return $this->formatearDatosPersona($persona);
        });
    }

    private function formatearDatosPersona($persona)
    {
        // Obtener datos con verificaciones de null
        $historial = $persona->historialActivo ?? null;
        $puesto = $historial->puesto ?? null;
        $unidad = $puesto->unidadOrganizacional ?? null;
        $cas = $persona->casActual ?? null;

        // Manejar fechas con null safety
        $fechaNacimiento = $persona->fechaNacimiento ?? null;
        $fechaIngreso = $historial->fecha_inicio ?? null;
        $fechaEmisionCas = $cas->fecha_emision_cas ?? null;

        return [
            // Datos básicos (siempre disponibles)
            'id' => $persona->id,
            'ci' => $persona->ci ?? '',
            'nombre_completo' => ($persona->nombre ?? '') . ' ' .
                               ($persona->apellidoPat ?? '') . ' ' .
                               ($persona->apellidoMat ?? ''),
            'nombre' => $persona->nombre ?? '',
            'apellido_paterno' => $persona->apellidoPat ?? '',
            'apellido_materno' => $persona->apellidoMat ?? '',
            'fecha_nacimiento' => $fechaNacimiento ? $fechaNacimiento->format('d/m/Y') : '',
            'edad' => $fechaNacimiento ? Carbon::parse($fechaNacimiento)->age: '',
            'sexo' => $persona->sexo ?? '',
            'telefono' => $persona->telefono ?? '',

            // Datos laborales (pueden ser null)
            'puesto' => $puesto->denominacion ?? 'Sin asignar',
            'unidad' => $unidad->nombre ?? 'Sin unidad',
            'nivel_jerarquico' => $puesto->nivelJerarquico ?? '',
            'salario' => $puesto ? number_format($puesto->haber, 2) : '0.00',
            'tipo_contrato' => $puesto->tipoContrato ?? '',
            'fecha_ingreso' => $fechaIngreso ? $fechaIngreso->format('d/m/Y') : '',
            'estado_laboral' => $historial ? ($historial->estado ?? '') : 'Sin historial',
            'es_jefatura' => $puesto ? ($puesto->esJefatura ? 'Sí' : 'No') : 'No',

            // CAS (puede ser null)
            'antiguedad_anios' => $cas->anios_servicio ?? '',
            'antiguedad_meses' => $cas->meses_servicio ?? '',
            'bono_antiguedad' => $cas ? number_format($cas->monto_bono, 2) : '0.00',
            'estado_cas' => $cas->estado_cas ?? '',
            'fecha_emision_cas' => $fechaEmisionCas ? $fechaEmisionCas->format('d/m/Y') : '',

            // Formación
            'profesiones' => $persona->profesiones->pluck('provisionN')->implode(', '),
            'universidades' => $persona->profesiones->pluck('universidad')->unique()->implode(', '),
            'registros_profesionales' => $persona->profesiones->pluck('registro')->filter()->implode(', '),
            'total_certificados' => $persona->certificados->count(),
            'licencia_militar' => $persona->licenciaMilitar->codigo ?? '',
        ];
    }

    private function getNombresColumnas()
    {
        return [
            'ci' => 'CI / Documento',
            'nombre_completo' => 'Nombre Completo',
            'nombre' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'edad' => 'Edad',
            'sexo' => 'Sexo',
            'telefono' => 'Teléfono',
            'puesto' => 'Puesto',
            'unidad' => 'Unidad Organizacional',
            'nivel_jerarquico' => 'Nivel Jerárquico',
            'salario' => 'Salario (Bs.)',
            'tipo_contrato' => 'Tipo de Contrato',
            'fecha_ingreso' => 'Fecha de Ingreso',
            'estado_laboral' => 'Estado Laboral',
            'es_jefatura' => 'Es Jefatura',
            'antiguedad_anios' => 'Años de Servicio',
            'antiguedad_meses' => 'Meses de Servicio',
            'bono_antiguedad' => 'Bono Antigüedad (Bs.)',
            'estado_cas' => 'Estado CAS',
            'fecha_emision_cas' => 'Fecha Emisión CAS',
            'profesiones' => 'Profesiones',
            'universidades' => 'Universidades',
            'registros_profesionales' => 'Registros Profesionales',
            'total_certificados' => 'Total Certificados',
            'licencia_militar' => 'Licencia Militar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2c3e50'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estilo para las celdas
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Alternar colores de filas
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:" . $lastColumn . $row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F8F9FA');
            }
        }

        // Ajustar altura de filas
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        return [];
    }

    public function columnFormats(): array
    {
        $formats = [];
        $columnaIndex = 0;

        foreach ($this->columnas as $columna) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex + 1);

            // Formato para columnas numéricas
            if (in_array($columna, ['salario', 'bono_antiguedad'])) {
                $formats[$columnLetter] = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2;
            }

            // Formato para fechas
            if (in_array($columna, ['fecha_nacimiento', 'fecha_ingreso', 'fecha_emision_cas'])) {
                $formats[$columnLetter] = 'dd/mm/yyyy';
            }

            $columnaIndex++;
        }

        return $formats;
    }
}
