<?php

namespace App\Exports;

use App\Models\Persona;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filtros;

    public function __construct($filtros = [])
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = Persona::with([
            'puestoActual.puesto.unidadOrganizacional',
            'profesion',
            'historials.puesto'
        ]);

        // Aplicar los mismos filtros que en el index
        if (!empty($this->filtros['search'])) {
            $query->where(function ($q) {
                $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%{$this->filtros['search']}%"])
                  ->orWhere('ci', 'LIKE', "%{$this->filtros['search']}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$this->filtros['search']}%")
                  ->orWhere('apellidoMat', 'LIKE', "%{$this->filtros['search']}%")
                  ->orWhereHas('profesion', function ($q2) {
                      $q2->where('provisionN', 'LIKE', "%{$this->filtros['search']}%")
                         ->orWhere('diploma', 'LIKE', "%{$this->filtros['search']}%");
                  })
                  ->orWhereHas('puestoActual.puesto', function ($q3) {
                      $q3->where('nombre', 'LIKE', "%{$this->filtros['search']}%")
                         ->orWhere('item', 'LIKE', "%{$this->filtros['search']}%");
                  });
            });
        }

        if (!empty($this->filtros['tipo']) && $this->filtros['tipo'] != 'TODOS') {
            $query->where('tipo', strtolower($this->filtros['tipo']));
        }

        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('fechaIngreso', '>=', Carbon::parse($this->filtros['fecha_inicio']));
        }

        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('fechaIngreso', '<=', Carbon::parse($this->filtros['fecha_fin']));
        }

        if (!empty($this->filtros['unidad_id'])) {
            $query->whereHas('puestoActual.puesto.unidadOrganizacional', function ($q) {
                $q->where('id', $this->filtros['unidad_id']);
            });
        }

        if (!empty($this->filtros['nivel_jerarquico'])) {
            $query->whereHas('puestoActual.puesto', function ($q) {
                $q->where('nivelJerarquico', 'LIKE', "%{$this->filtros['nivel_jerarquico']}%");
            });
        }

        if (isset($this->filtros['estado'])) {
            $query->where('estado', $this->filtros['estado']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'N°',
            'APELLIDO 1',
            'APELLIDO 2',
            'NOMBRE',
            'CI',
            'HABER',
            'FECHA INGRESO',
            'FECHA NACIMIENTO',
            'TITULO PROVISION NACIONAL',
            'FECHA TITULO',
            'TELEFONO',
            'ESTADO ACTUAL',
            'UNIDAD ORGANIZACIONAL',
            'ITEM',
            'NIVEL JERARQUICO'
        ];
    }

    public function map($persona): array
    {
        $haber = $persona->puestoActual && $persona->puestoActual->puesto
            ? number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.')
            : '0,00';

        $fechaIngreso = !empty($persona->fechaIngreso)
            ? Carbon::parse($persona->fechaIngreso)->format('d/m/Y')
            : '';

        $fechaNacimiento = !empty($persona->fechaNacimiento)
            ? Carbon::parse($persona->fechaNacimiento)->format('d/m/Y')
            : '';

        $fechaTitulo = !empty($persona->profesion->fechaProvision)
            ? Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y')
            : '';

        $estado = 'Sin puesto';
        if ($persona->puestoActual) {
            $estado = $persona->puestoActual->estado == 'activo' ? 'Activo' :
                     ($persona->puestoActual->estado == 'concluido' ? 'Concluido' :
                     ucfirst($persona->puestoActual->estado));
        }

        $unidad = $persona->puestoActual && $persona->puestoActual->puesto
            ? ($persona->puestoActual->puesto->unidadOrganizacional->denominacion ?? 'N/A')
            : 'N/A';

        $item = $persona->puestoActual && $persona->puestoActual->puesto
            ? ($persona->puestoActual->puesto->item ?? 'N/A')
            : 'N/A';

        $nivelJerarquico = $persona->puestoActual && $persona->puestoActual->puesto
            ? ($persona->puestoActual->puesto->nivelJerarquico ?? 'N/A')
            : 'N/A';

        return [
            $this->rowCount++,
            $persona->apellidoPat,
            $persona->apellidoMat,
            $persona->nombre,
            $persona->ci,
            $haber,
            $fechaIngreso,
            $fechaNacimiento,
            $persona->profesion->provisionN ?? '',
            $fechaTitulo,
            $persona->telefono,
            $estado,
            $unidad,
            $item,
            $nivelJerarquico
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el encabezado
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]
            ],
            // Autoajustar columnas
            'A:O' => [
                'alignment' => ['vertical' => 'center']
            ],
        ];
    }

    public function title(): string
    {
        return 'Reporte Personal';
    }

    private $rowCount = 1;
}
