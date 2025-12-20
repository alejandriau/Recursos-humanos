<?php

namespace App\Exports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportePersonalizadoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $columnas;
    protected $filtros;

    public function __construct($columnas = [], $filtros = [])
    {
        $this->columnas = $columnas;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = Persona::where('persona.estado', 1)
            ->with([
                'historialActivo.puesto.unidadOrganizacional',
                'casActual',
                'profesiones',
                'certificados'
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

        if (isset($this->filtros['sexo'])) {
            $query->where('sexo', $this->filtros['sexo']);
        }

        if (isset($this->filtros['busqueda'])) {
            $busqueda = $this->filtros['busqueda'];
            $query->where(function($q) use ($busqueda) {
                $q->where('ci', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$busqueda}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        $titulos = [
            'ci' => 'CI / DOCUMENTO',
            'nombre_completo' => 'NOMBRE COMPLETO',
            'fecha_nacimiento' => 'FECHA NACIMIENTO',
            'edad' => 'EDAD',
            'sexo' => 'SEXO',
            'telefono' => 'TELÉFONO',
            'puesto' => 'PUESTO ACTUAL',
            'unidad' => 'UNIDAD ORGANIZACIONAL',
            'nivel_jerarquico' => 'NIVEL JERÁRQUICO',
            'salario' => 'SALARIO (Bs.)',
            'tipo_contrato' => 'TIPO CONTRATO',
            'fecha_ingreso' => 'FECHA INGRESO',
            'estado_laboral' => 'ESTADO LABORAL',
            'es_jefatura' => 'ES JEFATURA',
            'antiguedad_anios' => 'AÑOS SERVICIO',
            'antiguedad_meses' => 'MESES SERVICIO',
            'bono_antiguedad' => 'BONO ANTIGÜEDAD',
            'estado_cas' => 'ESTADO CAS',
            'fecha_emision_cas' => 'FECHA EMISIÓN CAS',
            'profesiones' => 'PROFESIONES',
            'universidades' => 'UNIVERSIDADES',
            'registros_profesionales' => 'REGISTROS PROF.',
            'total_certificados' => 'TOTAL CERTIFICADOS',
            'licencia_militar' => 'LICENCIA MILITAR',
            'fecha_registro' => 'FECHA REGISTRO',
            'ultima_actualizacion' => 'ÚLTIMA ACTUALIZACIÓN',
            'estado_persona' => 'ESTADO PERSONA',
        ];

        $encabezados = [];
        foreach ($this->columnas as $columna) {
            $encabezados[] = $titulos[$columna] ?? strtoupper(str_replace('_', ' ', $columna));
        }

        return $encabezados;
    }

    public function map($persona): array
    {
        $datos = $this->formatearDatos($persona);
        $fila = [];

        foreach ($this->columnas as $columna) {
            $fila[] = $datos[$columna] ?? '';
        }

        return $fila;
    }

    private function formatearDatos($persona)
    {
        $puesto = $persona->historialActivo->puesto ?? null;
        $unidad = $puesto->unidadOrganizacional ?? null;
        $cas = $persona->casActual;

        return [
            'ci' => $persona->ci,
            'nombre_completo' => $persona->nombre . ' ' . $persona->apellidoPat . ' ' . ($persona->apellidoMat ?? ''),
            'fecha_nacimiento' => $persona->fechaNacimiento ? $persona->fechaNacimiento->format('d/m/Y') : '',
            'edad' => $persona->fechaNacimiento ? now()->diffInYears($persona->fechaNacimiento) : '',
            'sexo' => $persona->sexo,
            'telefono' => $persona->telefono,
            'puesto' => $puesto->denominacion ?? '',
            'unidad' => $unidad->nombre ?? '',
            'nivel_jerarquico' => $puesto->nivelJerarquico ?? '',
            'salario' => $puesto ? number_format($puesto->haber, 2) : '',
            'tipo_contrato' => $puesto->tipoContrato ?? '',
            'fecha_ingreso' => $persona->historialActivo->fecha_inicio ? $persona->historialActivo->fecha_inicio->format('d/m/Y') : '',
            'estado_laboral' => $persona->historialActivo->estado ?? '',
            'es_jefatura' => $puesto ? ($puesto->esJefatura ? 'Sí' : 'No') : '',
            'antiguedad_anios' => $cas->anios_servicio ?? '',
            'antiguedad_meses' => $cas->meses_servicio ?? '',
            'bono_antiguedad' => $cas ? number_format($cas->monto_bono, 2) : '',
            'estado_cas' => $cas->estado_cas ?? '',
            'fecha_emision_cas' => $cas && $cas->fecha_emision_cas ? $cas->fecha_emision_cas->format('d/m/Y') : '',
            'profesiones' => $persona->profesiones->pluck('universidad')->implode(', '),
            'universidades' => $persona->profesiones->pluck('universidad')->unique()->implode(', '),
            'registros_profesionales' => $persona->profesiones->pluck('registro')->filter()->implode(', '),
            'total_certificados' => $persona->certificados->count(),
            'licencia_militar' => $persona->licenciaMilitar->codigo ?? '',
            'fecha_registro' => $persona->fechaRegistro ? $persona->fechaRegistro->format('d/m/Y H:i') : '',
            'ultima_actualizacion' => $persona->fechaActualizacion ? $persona->fechaActualizacion->format('d/m/Y H:i') : '',
            'estado_persona' => $persona->estado == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para encabezados
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50']
                ],
                'alignment' => ['vertical' => 'center', 'horizontal' => 'center']
            ],

            // Estilo para filas alternas
            'A2:Z1000' => [
                'alignment' => ['vertical' => 'center']
            ],
        ];
    }

    public function columnWidths(): array
    {
        $anchuras = [
            'A' => 15, // CI
            'B' => 30, // Nombre completo
            'C' => 15, // Fecha nacimiento
            'D' => 10, // Edad
            'E' => 12, // Sexo
            'F' => 15, // Teléfono
            'G' => 35, // Puesto
            'H' => 25, // Unidad
            'I' => 20, // Nivel jerárquico
            'J' => 15, // Salario
            'K' => 15, // Tipo contrato
            'L' => 15, // Fecha ingreso
            'M' => 15, // Estado laboral
            'N' => 12, // Es jefatura
            'O' => 15, // Años servicio
            'P' => 15, // Meses servicio
            'Q' => 18, // Bono antigüedad
            'R' => 15, // Estado CAS
            'S' => 18, // Fecha emisión CAS
            'T' => 30, // Profesiones
            'U' => 25, // Universidades
            'V' => 20, // Registros prof.
            'W' => 18, // Total certificados
            'X' => 20, // Licencia militar
            'Y' => 20, // Fecha registro
            'Z' => 22, // Última actualización
        ];

        return $anchuras;
    }

    public function title(): string
    {
        return 'Reporte de Personal';
    }
}
