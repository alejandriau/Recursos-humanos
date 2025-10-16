<?php

namespace App\Http\Controllers;

use App\Models\PlanillasPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;

class PlanillasPdfController extends Controller
{
    public function index(Request $request)
    {
        // Obtener parámetros de ordenamiento
        $ordenCampo = $request->get('orden_campo', 'anio'); // Campo por defecto: anio
        $ordenDireccion = $request->get('orden_direccion', 'desc'); // Dirección por defecto: descendente

        // Validar campos de ordenamiento permitidos
        $camposPermitidos = ['anio', 'created_at', 'periodo_pago', 'nombre_original'];
        $ordenCampo = in_array($ordenCampo, $camposPermitidos) ? $ordenCampo : 'anio';

        // Validar dirección de ordenamiento
        $ordenDireccion = in_array($ordenDireccion, ['asc', 'desc']) ? $ordenDireccion : 'desc';

        // Consulta con ordenamiento dinámico
        $planillas = PlanillasPdf::orderBy($ordenCampo, $ordenDireccion)
                                ->orderBy('created_at', 'desc') // Orden secundario
                                ->paginate(10)
                                ->appends([
                                    'orden_campo' => $ordenCampo,
                                    'orden_direccion' => $ordenDireccion
                                ]);

        AuditService::logView('Vista de listado de planillas', null);

        return view('planillas-pdf.index', compact('planillas', 'ordenCampo', 'ordenDireccion'));
    }

    public function create()
    {
        return view('planillas-pdf.create');
    }

public function store(Request $request)
{
    $request->validate([
        'archivo_pdf' => 'required|file|mimes:pdf|max:10240',
        'periodo_pago' => 'nullable|string|max:100',
        'anio' => 'required|integer|min:2020|max:' . (date('Y') + 5),
        'fecha_elaboracion' => 'nullable|date',
        'total_empleados' => 'nullable|integer|min:0',
        'notas' => 'nullable|string|max:500'
    ]);

    try {
        $archivo = $request->file('archivo_pdf');
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreArchivo = time() . '_' . $nombreOriginal;
        $ruta = $archivo->storeAs('planillas', $nombreArchivo, 'public');

        // Extraer información del período de pago si no se proporcionó
        $periodoPago = $request->periodo_pago ?? $this->extraerPeriodoPago($nombreOriginal);

        // Determinar la fecha de elaboración
        $fechaElaboracion = $request->fecha_elaboracion ?: now();

        PlanillasPdf::create([
            'nombre_original' => $nombreOriginal,
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $ruta,
            'periodo_pago' => $periodoPago,
            'anio' => $request->anio,
            'fecha_elaboracion' => $fechaElaboracion,
            'total_empleados' => $request->total_empleados ?? 0,
            'notas' => $request->notas,
        ]);

        return redirect()->route('planillas-pdf.index')
            ->with('success', 'Planilla cargada exitosamente');

    } catch (\Exception $e) {
        Log::error('Error al cargar planilla: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error al cargar el archivo: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(PlanillasPdf $planilla)
    {
        AuditService::logView("Vista de planilla {$planilla->id}", $planilla);
        return view('planillas-pdf.show', compact('planilla'));
    }

    public function edit($id)
    {
        try {
            $planilla = PlanillasPdf::findOrFail($id);
            return view('planillas-pdf.edit', compact('planilla'));
        } catch (\Exception $e) {
            Log::error('Error al cargar edición de planilla: ' . $e->getMessage());
            return redirect()->route('planillas-pdf.index')
                ->with('error', 'Planilla no encontrada');
        }
    }

public function update(Request $request, $id)
{
    $request->validate([
        'nuevo_archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
        'periodo_pago' => 'nullable|string|max:100',
        'anio' => 'required|integer|min:2020|max:' . (date('Y') + 5),
        'fecha_elaboracion' => 'nullable|date',
        'total_empleados' => 'nullable|integer|min:0',
        'notas' => 'nullable|string|max:500'
    ]);

    try {
        $planilla = PlanillasPdf::findOrFail($id);
        $datosActualizacion = [];

        // Verificar si se está actualizando el archivo
        if ($request->hasFile('nuevo_archivo_pdf')) {
            $archivo = $request->file('nuevo_archivo_pdf');
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreArchivo = time() . '_' . $nombreOriginal;
            $ruta = $archivo->storeAs('planillas', $nombreArchivo, 'public');

            // Eliminar el archivo anterior
            if (Storage::disk('public')->exists($planilla->ruta)) {
                Storage::disk('public')->delete($planilla->ruta);
            }

            $datosActualizacion = [
                'nombre_original' => $nombreOriginal,
                'nombre_archivo' => $nombreArchivo,
                'ruta' => $ruta,
            ];

            // Si no se proporcionó período de pago, intentar extraerlo del nuevo archivo
            if (!$request->filled('periodo_pago')) {
                $periodoPago = $this->extraerPeriodoPago($nombreOriginal);
                if ($periodoPago) {
                    $datosActualizacion['periodo_pago'] = $periodoPago;
                }
            }
        }

        // Actualizar campos obligatorios y opcionales
        $datosActualizacion['anio'] = $request->anio;

        if ($request->filled('periodo_pago')) {
            $datosActualizacion['periodo_pago'] = $request->periodo_pago;
        }

        if ($request->filled('fecha_elaboracion')) {
            $datosActualizacion['fecha_elaboracion'] = $request->fecha_elaboracion;
        }

        if ($request->filled('total_empleados')) {
            $datosActualizacion['total_empleados'] = $request->total_empleados;
        }

        if ($request->filled('notas')) {
            $datosActualizacion['notas'] = $request->notas;
        }

        $datosActualizacion['updated_at'] = now();

        // Realizar la actualización
        $planilla->update($datosActualizacion);

        return redirect()->route('planillas-pdf.index')
            ->with('success', 'Planilla actualizada exitosamente');

    } catch (\Exception $e) {
        Log::error('Error al actualizar planilla: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error al actualizar la planilla: ' . $e->getMessage())
            ->withInput();
    }
}
    public function destroy($id)
    {
        try {
            $planilla = PlanillasPdf::findOrFail($id);

            // Eliminar el archivo físico
            if (Storage::disk('public')->exists($planilla->ruta)) {
                Storage::disk('public')->delete($planilla->ruta);
            }

            // Eliminar el registro de la base de datos
            $planilla->delete();

            return redirect()->route('planillas-pdf.index')
                ->with('success', 'Planilla eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar planilla: ' . $e->getMessage());
            return redirect()->route('planillas-pdf.index')
                ->with('error', 'Error al eliminar la planilla: ' . $e->getMessage());
        }
    }

    public function viewPdf($id)
    {
        $planilla = PlanillasPdf::findOrFail($id);

        if (!Storage::disk('public')->exists($planilla->ruta)) {
            abort(404, 'PDF no encontrado');
        }

        $filePath = storage_path('app/public/' . $planilla->ruta);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $planilla->nombre_original . '"'
        ]);
    }

    public function downloadPdf($id)
    {
        $planilla = PlanillasPdf::findOrFail($id);

        if (!Storage::disk('public')->exists($planilla->ruta)) {
            abort(404, 'PDF no encontrado');
        }

        return Storage::disk('public')->download($planilla->ruta, $planilla->nombre_original);
    }

    private function extraerPeriodoPago($nombreArchivo)
    {
        // Intenta extraer el período del nombre del archivo
        if (preg_match('/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/', $nombreArchivo, $matches)) {
            return $matches[1];
        }

        // Intentar extraer mes y año en español
        $meses = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];

        foreach ($meses as $mes) {
            if (stripos($nombreArchivo, $mes) !== false && preg_match('/\d{4}/', $nombreArchivo, $anoMatches)) {
                return ucfirst($mes) . ' ' . $anoMatches[0];
            }
        }

        return 'Período no especificado';
    }
}
