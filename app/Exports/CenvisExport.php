<?php

namespace App\Exports;

use App\Models\Cenvi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CenvisExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Cenvi::with('persona');

        // Buscar por persona (igual que en el controlador)
        if ($this->request->filled('nombre')) {
            $query->whereHas('persona', function ($q) {
                $q->where('nombre', 'like', "%{$this->request->nombre}%")
                  ->orWhere('apellidoPat', 'like', "%{$this->request->nombre}%")
                  ->orWhere('apellidoMat', 'like', "%{$this->request->nombre}%");
            });
        }

        // Filtros por fecha
        if ($this->request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $this->request->fecha_desde);
        }
        if ($this->request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $this->request->fecha_hasta);
        }

        // Filtro por vigencia
        switch ($this->request->vigencia) {
            case 'vigentes':
                $query->vigentes();
                break;
            case 'vencidos':
                $query->vencidos();
                break;
            case 'por_vencer':
                $query->porVencer();
                break;
        }

        // Orden (igual al index)
        $orderBy = $this->request->get('order_by', 'fecha');
        $orderDirection = $this->request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Persona (CI)',
            'Apellido Paterno',
            'Apellido Materno',
            'Nombre(s)',
            'Fecha del CENVI',
            'Observación',
            'Estado Vigencia',
            'Fecha Vencimiento (calculada)',
            'Ruta del PDF'
        ];
    }

    public function map($cenvi): array
    {
        $persona = $cenvi->persona;
        
        // Calcular estado de vigencia (usando la lógica del modelo)
        $estado = '';
        $fechaVencimiento = '';
        
        if ($cenvi->fecha) {
            $vencimiento = Carbon::parse($cenvi->fecha)->addYear(); // 1 año de vigencia
            $fechaVencimiento = $vencimiento->format('d/m/Y');
            $hoy = Carbon::now();
            
            if ($vencimiento < $hoy) {
                $estado = 'Vencido';
            } elseif ($vencimiento <= $hoy->copy()->addDays(30)) {
                $estado = 'Por vencer (30 días)';
            } else {
                $estado = 'Vigente';
            }
        } else {
            $estado = 'Sin fecha';
        }

        return [
            $cenvi->id,
            $persona ? ($persona->ci ?? '') : '',
            $persona ? ($persona->apellidoPat ?? '') : '',
            $persona ? ($persona->apellidoMat ?? '') : '',
            $persona ? ($persona->nombre ?? '') : '',
            $cenvi->fecha ? Carbon::parse($cenvi->fecha)->format('d/m/Y') : '',
            $cenvi->observacion ?? '',
            $estado,
            $fechaVencimiento,
            $cenvi->pdf_cenvi,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}