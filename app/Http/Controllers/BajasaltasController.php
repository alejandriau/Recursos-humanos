<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Bajasaltas;
use App\Models\Pasivodos;
use App\Models\Historial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;


class BajasaltasController extends Controller
{
public function index(Request $request)
    {
        $query = Bajasaltas::with(['persona' => function($query) {
                $query->select('id', 'nombre', 'apellidoPat', 'apellidoMat', 'ci', 'fechaNacimiento', 'fechaIngreso', 'foto');
            }])
            ->where('estado', 1)
            ->orderBy('fecha', 'desc');

        // Filtros
        if ($request->filled('nombre')) {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidoPat', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidoMat', 'like', '%' . $request->nombre . '%');
            });
        }

        if ($request->filled('desde')) {
            $query->where('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', $request->hasta);
        }

        if ($request->filled('motivo')) {
            $query->where('motivo', $request->motivo);
        }

        $bajas = $query->paginate(10)->withQueryString();

        // Procesar datos para la vista
        $bajasProcesadas = $bajas->getCollection()->map(function($baja) {
            $fechaIngreso = $baja->persona->fechaIngreso ? Carbon::parse($baja->persona->fechaIngreso) : null;
            $fechaBaja = Carbon::parse($baja->fecha);

            $tiempo = 'N/A';
            if ($fechaIngreso) {
                $diferencia = $fechaIngreso->diff($fechaBaja);
                $tiempo = $diferencia->y . ' años, ' . $diferencia->m . ' meses, ' . $diferencia->d . ' días';
            }

            return [
                'id' => $baja->id, // ID de la baja
                'persona_id' => $baja->persona->id, // ID de la persona - ¡IMPORTANTE!
                'nombre' => $baja->persona->nombre . ' ' . $baja->persona->apellidoPat . ' ' . $baja->persona->apellidoMat,
                'ci' => $baja->persona->ci,
                'foto' => $baja->persona->foto,
                'fecha_nacimiento' => $baja->persona->fechaNacimiento,
                'fecha_ingreso' => $baja->persona->fechaIngreso,
                'fecha_baja' => $baja->fecha,
                'motivo' => $baja->motivo,
                'observacion' => $baja->observacion,
                'pdfbaja' => $baja->pdfbaja,
                'tiempo_en_institucion' => $tiempo
            ];
        });

        $bajas->setCollection($bajasProcesadas);

        // Estadísticas
        $estadisticas = [
            'total' => Bajasaltas::where('estado', 1)->count(),
            'este_mes' => Bajasaltas::where('estado', 1)
                ->whereYear('fecha', date('Y'))
                ->whereMonth('fecha', date('m'))
                ->count(),
            'con_pdf' => Bajasaltas::where('estado', 1)->whereNotNull('pdfbaja')->count(),
            'sin_pdf' => Bajasaltas::where('estado', 1)->whereNull('pdfbaja')->count(),
        ];

        return view('admin.bajas.index', compact('bajas', 'estadisticas'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'idPersona' => 'required|exists:persona,id', // Cambiado a 'personas'
        'fechafin' => 'required|date',
        'motivo' => 'required|string',
        'apellidopaterno' => 'nullable|string',
        'apellidomaterno' => 'nullable|string',
        'nombre' => 'nullable|string',
        'obser' => 'nullable|string',
        'pdffile' => 'nullable|file|mimes:pdf|max:2048',
        'idHistorial' => 'nullable|exists:historials,id' // Cambiado a 'historiales'
    ]);

    // Debug: Verificar los datos recibidos
    \Log::info('Datos recibidos en store:', $validated);

    try {
        // Verificar que la persona existe antes de continuar
        $persona = Persona::find($validated['idPersona']);
        if (!$persona) {
            return back()->with('error', 'La persona no existe en el sistema.')->withInput();
        }

        // Cerrar historial si se proporciona idHistorial
        if (!empty($validated['idHistorial'])) {
            $historial = Historial::find($validated['idHistorial']);

            if ($historial) {
                if (is_null($historial->fecha_fin)) {
                    $historial->fecha_fin = $validated['fechafin'] ?? now();
                    $historial->estado = 'concluido';
                    $historial->save();
                    \Log::info('Historial actualizado:', ['id' => $historial->id, 'fecha_fin' => $historial->fecha_fin]);
                }
            } else {
                \Log::warning('Historial no encontrado:', ['idHistorial' => $validated['idHistorial']]);
            }
        }

        $nombreCompleto = trim(($validated['apellidopaterno'] ?? '') . " " .
                             ($validated['apellidomaterno'] ?? '') . " " .
                             ($validated['nombre'] ?? ''));

        // Obtener la primera letra del apellido paterno o materno
        $letra = substr($validated['apellidopaterno'] ?? ($validated['apellidomaterno'] ?? 'A'), 0, 1);

        \Log::info('Procesando pasivo:', ['nombre' => $nombreCompleto, 'letra' => $letra]);

        // Reutilizar o insertar nuevo en pasivodos
        $pasivo = Pasivodos::where('letra', $letra)->whereNull('nombrecompleto')->first();

        if ($pasivo) {
            $pasivo->nombrecompleto = $nombreCompleto;
            $pasivo->save();
            \Log::info('Pasivo actualizado:', ['id' => $pasivo->id]);
        } else {
            $maxCodigo = Pasivodos::where('letra', $letra)->max('codigo') ?? 0;
            $nuevoPasivo = Pasivodos::create([
                'codigo' => $maxCodigo + 1,
                'nombrecompleto' => $nombreCompleto,
                'letra' => $letra
            ]);
            \Log::info('Nuevo pasivo creado:', ['id' => $nuevoPasivo->id]);
        }

        // Cambiar estado de la persona
        $persona->estado = 0;
        $persona->save();
        \Log::info('Estado de persona actualizado:', ['id' => $persona->id, 'estado' => 0]);

        // Guardar archivo si existe
        $pdfPath = null;
        if ($request->hasFile('pdffile')) {
            // Crear subcarpeta para bajas si no existe
            $rutaBajas = $persona->archivo . '/bajas';
            if (!Storage::disk('local')->exists($rutaBajas)) {
                Storage::disk('local')->makeDirectory($rutaBajas);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = "BAJA_" . $persona->ci . "_" .
                           now()->format('YmdHis') . "_" .
                           Str::slug($validated['motivo'], '_') . ".pdf";

            // Guardar archivo
            $path = $request->file('pdffile')->storeAs($rutaBajas, $nombreArchivo, 'local');
            $pdfPath = $path;

            \Log::info('PDF de baja guardado:', [
                'ruta' => $pdfPath,
                'persona_id' => $persona->id
            ]);
        }

        // Registrar baja
        $baja = Bajasaltas::create([
            'idPersona' => $validated['idPersona'],
            'fecha' => $validated['fechafin'],
            'motivo' => $validated['motivo'],
            'fecharegistro' => now(),
            'observacion' => $validated['obser'] ?? null,
            'pdfbaja' => $pdfPath
        ]);

        \Log::info('Baja registrada:', ['id' => $baja->id]);

        return redirect()->route('altasbajas')->with('success', 'Baja registrada correctamente');

    } catch (\Exception $e) {
        \Log::error('Error al registrar baja:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Error al registrar la baja: ' . $e->getMessage())->withInput();
    }
}
    public function edit(string $id)
    {
        try {
            $baja = Bajasaltas::with('persona')->findOrFail($id);

            // Formatear datos para la vista
            $datosBaja = [
                'id' => $baja->id,
                'idPersona' => $baja->idPersona,
                'fecha_baja' => $baja->fecha,
                'motivo' => $baja->motivo,
                'observacion' => $baja->observacion,
                'pdfbaja' => $baja->pdfbaja,
                'nombre_completo' => $baja->persona ?
                    $baja->persona->nombre . ' ' . $baja->persona->apellidopaterno . ' ' . $baja->persona->apellidomaterno :
                    'N/A'
            ];

            \Log::info('Datos cargados para edición:', $datosBaja);

            return response()->json([
                'success' => true,
                'data' => $datosBaja
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en método edit:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos para edición'
            ], 404);
        }
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string',
            'observacion' => 'nullable|string',
            'pdffile' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // Debug: Verificar los datos recibidos
        \Log::info('Datos recibidos en update:', array_merge($validated, ['id' => $id]));

        try {
            // Buscar la baja existente
            $baja = Bajasaltas::findOrFail($id);
            \Log::info('Baja encontrada:', ['id' => $baja->id, 'idPersona' => $baja->idPersona]);

            // Obtener la persona relacionada
            $persona = Persona::find($baja->idPersona);
            if (!$persona) {
                return back()->with('error', 'La persona asociada a esta baja no existe.')->withInput();
            }

            // Actualizar el historial laboral si existe
            $historial = Historial::where('idPersona', $baja->idPersona)
                                ->where('estado', 'concluido')
                                ->latest()
                                ->first();

            if ($historial) {
                $historial->fecha_fin = $validated['fecha'];
                $historial->save();
                \Log::info('Historial actualizado:', ['id' => $historial->id, 'fecha_fin' => $historial->fecha_fin]);
            }

            // Actualizar el registro en pasivodos si existe
            $nombreCompleto = trim(($persona->apellidopaterno ?? '') . " " .
                                ($persona->apellidomaterno ?? '') . " " .
                                ($persona->nombre ?? ''));

            $letra = substr($persona->apellidopaterno ?? ($persona->apellidomaterno ?? 'A'), 0, 1);

            $pasivo = Pasivodos::where('nombrecompleto', 'like', '%' . $nombreCompleto . '%')
                            ->orWhere('letra', $letra)
                            ->first();

            if ($pasivo) {
                // Si encontramos un pasivo, actualizamos el nombre completo por si hubo cambios
                $pasivo->nombrecompleto = $nombreCompleto;
                $pasivo->save();
                \Log::info('Pasivo actualizado:', ['id' => $pasivo->id]);
            }

            // Manejar archivo PDF igual que en store
            $pdfPath = $baja->pdfbaja; // Mantener el PDF existente por defecto

            if ($request->hasFile('pdffile')) {
                // Verificar/crear estructura de carpetas de la persona
                if (!$persona->archivo) {
                    $nombre = Str::slug($persona->nombre);
                    $apellidoMat = Str::slug($persona->apellidomaterno ?? 'SinApellido');
                    $fecha = now()->format('Y-m-d');
                    $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
                    $rutaBase = "archivos/{$nombreCarpeta}";

                    // Crear carpeta principal y subcarpetas
                    Storage::disk('local')->makeDirectory($rutaBase);

                    // Actualizar persona con la ruta
                    $persona->archivo = $rutaBase;
                    $persona->save();
                    \Log::info('Carpeta creada para persona:', ['ruta' => $rutaBase]);
                }

                // Crear subcarpeta para bajas si no existe (igual que en store)
                $rutaBajas = $persona->archivo . '/bajas';
                if (!Storage::disk('local')->exists($rutaBajas)) {
                    Storage::disk('local')->makeDirectory($rutaBajas);
                }

                // Generar nombre único para el archivo (igual que en store)
                $nombreArchivo = "BAJA_" . $persona->ci . "_" .
                            now()->format('YmdHis') . "_" .
                            Str::slug($validated['motivo'], '_') . ".pdf";

                // Guardar archivo en el disco local (igual que en store)
                $path = $request->file('pdffile')->storeAs($rutaBajas, $nombreArchivo, 'local');
                $pdfPath = $path;

                // Eliminar archivo anterior si existe
                if ($baja->pdfbaja && Storage::disk('local')->exists($baja->pdfbaja)) {
                    Storage::disk('local')->delete($baja->pdfbaja);
                    \Log::info('PDF anterior eliminado:', ['ruta' => $baja->pdfbaja]);
                } elseif ($baja->pdfbaja && Storage::disk('public')->exists($baja->pdfbaja)) {
                    // Para compatibilidad: si el archivo antiguo está en 'public', eliminarlo de allí también
                    Storage::disk('public')->delete($baja->pdfbaja);
                    \Log::info('PDF anterior eliminado de public:', ['ruta' => $baja->pdfbaja]);
                }

                \Log::info('Nuevo PDF guardado:', ['ruta' => $pdfPath]);
            }

            // Actualizar el registro de baja
            $baja->update([
                'fecha' => $validated['fecha'],
                'motivo' => $validated['motivo'],
                'observacion' => $validated['observacion'] ?? null,
                'pdfbaja' => $pdfPath
            ]);

            \Log::info('Baja actualizada correctamente:', ['id' => $baja->id]);

            return redirect()->route('bajasaltas.index')
                            ->with('success', 'Baja actualizada correctamente');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar baja:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al actualizar la baja: ' . $e->getMessage())->withInput();
        }
    }
    public function show(string $id)
    {
        try {
            $baja = Bajasaltas::with(['persona', 'historial'])->findOrFail($id);

            // Buscar historial laboral
            $historial = Historial::where('idPersona', $baja->idPersona)
                                ->where('estado', 'concluido')
                                ->latest()
                                ->first();

            // Calcular tiempo en la institución
            $tiempoInstitucion = 'N/A';
            if ($historial && $historial->fecha_inicio && $baja->fecha) {
                $fechaInicio = Carbon::parse($historial->fecha_inicio);
                $fechaFin = Carbon::parse($baja->fecha);

                $anos = $fechaFin->diffInYears($fechaInicio);
                $meses = $fechaFin->diffInMonths($fechaInicio) % 12;
                $dias = $fechaFin->diffInDays($fechaInicio) % 30;

                $tiempoInstitucion = $anos . ' años, ' . $meses . ' meses, ' . $dias . ' días';
            }

            $datosBaja = [
                'id' => $baja->id,
                'nombre' => $baja->persona ?
                    $baja->persona->nombre . ' ' . $baja->persona->apellidopaterno . ' ' . $baja->persona->apellidomaterno :
                    'N/A',
                'ci' => $baja->persona ? $baja->persona->ci : 'N/A',
                'foto' => $baja->persona ? $baja->persona->foto : null,
                'fecha_nacimiento' => $baja->persona ? $baja->persona->fechanacimiento : null,
                'fecha_ingreso' => $historial ? $historial->fecha_inicio : null,
                'fecha_baja' => $baja->fecha,
                'motivo' => $baja->motivo,
                'observacion' => $baja->observacion,
                'pdfbaja' => $baja->pdfbaja,
                'tiempo_en_institucion' => $tiempoInstitucion
            ];

            return response()->json([
                'success' => true,
                'data' => $datosBaja
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos de la baja'
            ], 404);
        }
    }
    public function verPdf($id)
    {
        $baja = Bajasaltas::findOrFail($id);

        // Verifica que exista el PDF
        if (!$baja->pdfbaja || !Storage::exists($baja->pdfbaja)) {
            abort(404, 'PDF no encontrado');
        }

        // Obtiene contenido y tipo MIME
        $contenido = Storage::get($baja->pdfbaja);
        $tipo = Storage::mimeType($baja->pdfbaja);

        // Devuelve el PDF para ver en el navegador
        return response($contenido)
            ->header('Content-Type', $tipo)
            ->header('Content-Disposition', 'inline; filename="' . basename($baja->pdfbaja) . '"');
    }

    public function descargarPdf($id)
    {
        $baja = Bajasaltas::findOrFail($id);

        if (!$baja->pdfbaja || !Storage::exists($baja->pdfbaja)) {
            abort(404, 'PDF no encontrado');
        }

        $contenido = Storage::get($baja->pdfbaja);
        $nombreArchivo = 'baja_' . $baja->id . '.pdf';

        return response($contenido)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
    }



}
