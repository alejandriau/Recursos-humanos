<?php

namespace App\Exports;

use App\Models\Certificado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CertificadosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Certificado::with('persona')->where('estado', 1);

        // Aplicar los mismos filtros que en el index
        if ($this->request->filled('buscar')) {
            $buscar = $this->request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('persona', function ($q2) use ($buscar) {
                    $q2->where('nombre', 'like', "%$buscar%")
                        ->orWhere('apellidoPat', 'like', "%$buscar%")
                        ->orWhere('apellidoMat', 'like', "%$buscar%");
                })
                ->orWhere('nombre', 'like', "%$buscar%");
            });
        }

        if ($this->request->filled('desde')) {
            $query->whereDate('fecha', '>=', $this->request->desde);
        }

        if ($this->request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $this->request->hasta);
        }

        if ($this->request->filled('categoria')) {
            $query->where('categoria', $this->request->categoria);
        }

        if ($this->request->filled('estado_vencimiento')) {
            switch ($this->request->estado_vencimiento) {
                case 'vencidos':
                    $query->whereNotNull('fecha_vencimiento')
                        ->whereDate('fecha_vencimiento', '<', Carbon::now());
                    break;
                case 'por_vencer':
                    $query->whereNotNull('fecha_vencimiento')
                        ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
                        ->whereDate('fecha_vencimiento', '>=', Carbon::now());
                    break;
                case 'vigentes':
                    $query->where(function ($q) {
                        $q->whereNull('fecha_vencimiento')
                            ->orWhereDate('fecha_vencimiento', '>=', Carbon::now());
                    });
                    break;
                case 'sin_vencimiento':
                    $query->whereNull('fecha_vencimiento');
                    break;
            }
        }

        // Mismo orden que en el index
        $query->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END')
              ->orderBy('fecha_vencimiento');

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del certificado',
            'Tipo',
            'Categoría',
            'Fecha emisión',
            'Institución',
            'Fecha vencimiento',
            'Estado vencimiento',
            'CI (Cédula identidad)',
            'Apellido Paterno',
            'Apellido Materno',
            'Nombre(s)',
            'Ruta del PDF'
        ];
    }

    public function map($certificado): array
    {
        // Calcular estado de vencimiento
        $estadoVencimiento = '';
        if ($certificado->categoria === 'quechua') {
            if ($certificado->fecha_vencimiento) {
                $hoy = Carbon::now();
                if ($certificado->fecha_vencimiento < $hoy) {
                    $estadoVencimiento = 'Vencido';
                } elseif ($certificado->fecha_vencimiento <= $hoy->copy()->addDays(30)) {
                    $estadoVencimiento = 'Por vencer (30 días)';
                } else {
                    $estadoVencimiento = 'Vigente';
                }
            } else {
                $estadoVencimiento = 'Sin fecha de vencimiento';
            }
        } else {
            $estadoVencimiento = 'No aplica';
        }

        $persona = $certificado->persona;
        
        // Datos de persona limpios
        $ci = $persona ? trim($persona->ci ?? '') : '';
        $apellidoPat = $persona ? trim($persona->apellidoPat ?? '') : '';
        $apellidoMat = $persona ? trim($persona->apellidoMat ?? '') : '';
        $nombre = $persona ? trim($persona->nombre ?? '') : '';

        return [
            $certificado->id,
            $certificado->nombre,
            $certificado->tipo,
            $certificado->categoria,
            $certificado->fecha ? Carbon::parse($certificado->fecha)->format('d/m/Y') : '',
            $certificado->instituto,
            $certificado->fecha_vencimiento ? Carbon::parse($certificado->fecha_vencimiento)->format('d/m/Y') : '',
            $estadoVencimiento,
            $ci,                      // CI separado
            $apellidoPat,            // Apellido paterno
            $apellidoMat,            // Apellido materno
            $nombre,                 // Nombre(s)
            $certificado->pdfcerts,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],  // Primera fila en negrita
        ];
    }
}