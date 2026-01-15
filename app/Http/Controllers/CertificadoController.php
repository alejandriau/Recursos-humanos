<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificado;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificado::with('persona')->where('estado', 1);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('persona', function ($q2) use ($buscar) {
                    $q2->where('nombre', 'like', "%$buscar%")
                        ->orWhere('apellidoPat', 'like', "%$buscar%")
                        ->orWhere('apellidoMat', 'like', "%$buscar%");
                })
                ->orWhere('nombre', 'like', "%$buscar%");
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        // NUEVOS FILTROS
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('estado_vencimiento')) {
            switch ($request->estado_vencimiento) {
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

        // Ordenar por fecha de vencimiento (los que están por vencer primero)
        $query->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END')
            ->orderBy('fecha_vencimiento');

        $certificados = $query->get();
        $categorias = ['quechua', 'ley_1178', 'politicas_publicas', 'responsabilidad_funcion_publica', 'otros'];

        return view('admin.certificados.index', compact('certificados', 'categorias'));
    }
    public function create(Request $request)
    {
        $personas = Persona::all();
        $idPersona = $request->get('idPersona'); // capturar persona previa

        return view('admin.certificados.create', compact('personas', 'idPersona'));
    }
    public function edit(Certificado $certificado)
    {
        // No necesitas cargar todas las personas, solo el certificado con su persona
        $certificado->load('persona');

        return view('admin.certificados.edit', compact('certificado'));
    }
public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'tipo' => 'nullable|string|max:100',
        'categoria' => 'required|in:quechua,ley_1178,politicas_publicas,responsabilidad_funcion_publica,otros',
        'fecha' => 'nullable|date',
        'instituto' => 'nullable|string|max:255',
        'pdfcerts' => 'nullable|file|mimes:pdf|max:2048',
        'idPersona' => 'required|exists:personas,id',
    ]);

    try {
        $persona = Persona::findOrFail($request->idPersona);

        // Verificar si la persona tiene una ruta base definida
        if (empty($persona->archivo)) {
            // Si no tiene ruta, crear una basada en su ID o CI
            $persona->archivo = 'archivos/' . ($persona->ci ?? $persona->id);
            $persona->save();
        }

        // Crear certificado
        $certificado = new Certificado();
        $certificado->nombre = $request->nombre;
        $certificado->tipo = $request->tipo;
        $certificado->categoria = $request->categoria;
        $certificado->fecha = $request->fecha;
        $certificado->instituto = $request->instituto;
        $certificado->idPersona = $request->idPersona;

        // Calcular fecha de vencimiento si es certificado de quechua
        if ($request->categoria === 'quechua' && $request->fecha) {
            $certificado->fecha_vencimiento = \Carbon\Carbon::parse($request->fecha)->addYears(3);
        }

        // Guardar archivo PDF en la ruta directa de la persona
        if ($request->hasFile('pdfcerts')) {
            // Crear carpeta de la persona si no existe
            if (!Storage::disk('local')->exists($persona->archivo)) {
                Storage::disk('local')->makeDirectory($persona->archivo);
                \Log::info('Directorio de persona creado:', ['ruta' => $persona->archivo]);
            }

            // Generar nombre único para el archivo
            $fechaActual = now()->format('Ymd_His');
            $nombreArchivo = "CERT_" . $request->categoria . "_" .
                           $fechaActual . "_" .
                           \Illuminate\Support\Str::slug($request->nombre, '_') . ".pdf";

            // Guardar archivo directamente en la carpeta de la persona
            $path = $request->file('pdfcerts')->storeAs(
                $persona->archivo,
                $nombreArchivo,
                'local'
            );

            $certificado->pdfcerts = $path;

            \Log::info('PDF de certificado guardado:', [
                'ruta' => $path,
                'persona_id' => $persona->id,
                'carpeta' => $persona->archivo,
                'archivo' => $nombreArchivo
            ]);
        }

        $certificado->save();

        \Log::info('Certificado creado:', [
            'id' => $certificado->id,
            'persona_id' => $persona->id,
            'categoria' => $request->categoria,
            'ruta_pdf' => $certificado->pdfcerts
        ]);

        return redirect()->route('certificados.index')->with('success', 'Certificado guardado correctamente.');

    } catch (\Exception $e) {
        \Log::error('Error al guardar certificado:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Error al guardar el certificado: ' . $e->getMessage())->withInput();
    }
}

public function update(Request $request, Certificado $certificado)
{
    $data = $request->validate([
        'nombre' => 'required|string|max:500',
        'tipo' => 'nullable|string|max:80',
        'categoria' => 'required|in:quechua,ley_1178,politicas_publicas,responsabilidad_funcion_publica,otros',
        'fecha' => 'nullable|date',
        'instituto' => 'nullable|string|max:80',
        'pdfcerts' => 'nullable|file|mimes:pdf|max:10240',
        'idPersona' => 'required|integer|exists:personas,id',
        'eliminar_pdf' => 'nullable|boolean'
    ]);

    try {
        $persona = Persona::findOrFail($data['idPersona']);

        // Calcular fecha de vencimiento
        if ($data['categoria'] === 'quechua' && $data['fecha']) {
            $data['fecha_vencimiento'] = \Carbon\Carbon::parse($data['fecha'])->addYears(3);
        } else {
            $data['fecha_vencimiento'] = null;
        }

        // Manejo del archivo PDF
        if ($request->has('eliminar_pdf') && $request->eliminar_pdf == 1) {
            // Eliminar archivo actual si existe
            if ($certificado->pdfcerts && Storage::disk('local')->exists($certificado->pdfcerts)) {
                Storage::disk('local')->delete($certificado->pdfcerts);
                \Log::info('PDF eliminado:', ['ruta' => $certificado->pdfcerts]);
            }
            $data['pdfcerts'] = null;
        }

        if ($request->hasFile('pdfcerts')) {
            // Eliminar archivo anterior si existe
            if ($certificado->pdfcerts && Storage::disk('local')->exists($certificado->pdfcerts)) {
                Storage::disk('local')->delete($certificado->pdfcerts);
            }

            // Crear carpeta de la persona si no existe
            if (!Storage::disk('local')->exists($persona->archivo)) {
                Storage::disk('local')->makeDirectory($persona->archivo);
            }

            // Generar nombre único para el archivo
            $fechaActual = now()->format('Ymd_His');
            $nombreArchivo = "CERT_" . $data['categoria'] . "_" .
                           $fechaActual . "_" .
                           \Illuminate\Support\Str::slug($data['nombre'], '_') . ".pdf";

            // Guardar archivo directamente en la carpeta de la persona
            $path = $request->file('pdfcerts')->storeAs(
                $persona->archivo,
                $nombreArchivo,
                'local'
            );

            $data['pdfcerts'] = $path;

            \Log::info('PDF de certificado actualizado:', [
                'ruta' => $path,
                'certificado_id' => $certificado->id,
                'carpeta_persona' => $persona->archivo
            ]);
        } else {
            // Mantener el archivo actual si no se sube uno nuevo
            unset($data['pdfcerts']);
        }

        $certificado->update($data);

        \Log::info('Certificado actualizado:', [
            'id' => $certificado->id,
            'categoria' => $data['categoria']
        ]);

        return redirect()->route('certificados.index')->with('success', 'Certificado actualizado correctamente.');

    } catch (\Exception $e) {
        \Log::error('Error al actualizar certificado:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Error al actualizar el certificado: ' . $e->getMessage())->withInput();
    }
}
    // Método para reporte de certificados próximos a vencer
    public function reporteVencimientos()
    {
        $quechuasPorVencer = Certificado::quechua()->porVencer(30)->with('persona')->get();
        $quechuasVencidos = Certificado::quechua()->vencidos()->with('persona')->get();

        return view('admin.certificados.reporte-vencimientos', compact('quechuasPorVencer', 'quechuasVencidos'));
    }
        public function destroy(Certificado $certificado)
    {
        $certificado->estado = 0;
        $certificado->save();
        return redirect()->route('certificados.index')->with('success', 'Certificado eliminado correctamente.');
    }
}
