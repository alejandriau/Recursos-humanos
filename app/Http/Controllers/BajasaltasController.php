<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Bajasaltas;
use App\Models\Pasivodos;
use App\Models\Historial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

        $bajas = $query->paginate(15);

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
                'id' => $baja->id,
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
            $pdfPath = $request->file('pdffile')->store('pdfs', 'public');
            \Log::info('PDF guardado:', ['ruta' => $pdfPath]);
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
        public function update(Request $request, $id)
    {
        $baja = Bajasaltas::findOrFail($id);

        // Validación
        $request->validate([
            'motivo' => 'required|string|max:255',
            'fecha_baja' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        // Actualización
        $baja->motivo = $request->motivo;
        $baja->fecha = $request->fecha_baja;
        $baja->observacion = $request->observaciones;
        $baja->save();

        return redirect()->route('bajasaltas.index')->with('mensaje', 'Registro de baja actualizado correctamente.');
    }
}
