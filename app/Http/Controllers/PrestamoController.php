<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Pasivodos;  // <--- IMPORTANTE: Usa el namespace completo
use App\Models\Pasivouno;  // Si también necesitas el otro tipo
use App\Models\profesion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;  // <--- IMPORTANTE: Agrega esta línea
use Illuminate\Support\Facades\DB;   // Opcional, si usas DB
use App\Notifications\NuevaSolicitudPrestamo;
use App\Notifications\PrestamoAprobado;
use App\Notifications\PrestamoDevuelto;
use App\Notifications\PrestamoEntregado;
use App\Notifications\PrestamoRechazado;

class PrestamoController extends Controller
{


    /**
     * Listado de préstamos con filtros
     */
    public function index()
    {
        // Contar préstamos por estado
        $stats = [
            'pendientes' => Prestamo::where('estado', 'pendiente')->count(),
            'aprobados' => Prestamo::where('estado', 'aprobado')->count(),
            'prestados' => Prestamo::where('estado', 'prestado')->count(),
            'devueltos' => Prestamo::where('estado', 'devuelto')->count(),
            'vencidos' => Prestamo::where('estado', 'vencido')->count(),
            'rechazados' => Prestamo::where('estado', 'rechazado')->count(),
        ];

        // Obtener solicitudes pendientes
        $pendientes = Prestamo::with(['carpeta', 'solicitante'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener préstamos activos (aprobados y prestados)
        $activos = Prestamo::with(['carpeta', 'solicitante'])
            ->whereIn('estado', ['aprobado', 'prestado'])
            ->orderBy('fecha_prestamo', 'desc')
            ->get();

        // Obtener historial reciente
        $historial = Prestamo::with(['carpeta', 'solicitante'])
            ->whereIn('estado', ['devuelto', 'rechazado', 'vencido'])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.prestamos.index', compact('stats', 'pendientes', 'activos', 'historial'));
    }

    /**
     * Obtener préstamos del usuario actual
     */
public function misPrestamos(Request $request)
{
    try {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // Cargar todas las relaciones necesarias
        $prestamos = Prestamo::with(['carpeta', 'solicitante', 'archivero', 'usuarioRegistrador'])
            ->where('solicitante_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

$data = $prestamos->map(function ($prestamo) {
    return [
        'id' => $prestamo->id,
        'carpeta_id' => $prestamo->carpeta_id,
        'carpeta_type' => $prestamo->carpeta_type,

        'fecha_solicitud' => optional($prestamo->fecha_solicitud)->format('d/m/Y'),
        'fecha_prestamo' => optional($prestamo->fecha_prestamo)->format('d/m/Y'),
        'fecha_devolucion_estimada' => optional($prestamo->fecha_devolucion_estimada)->format('d/m/Y'),
        'fecha_devolucion_real' => optional($prestamo->fecha_devolucion_real)->format('d/m/Y'),
        
        // Agregar los campos formateados que usa tu frontend
        'fecha_solicitud_formateada' => $prestamo->fecha_solicitud_formateada,
        'fecha_prestamo_formateada' => $prestamo->fecha_prestamo_formateada,
        'fecha_devolucion_estimada_formateada' => $prestamo->fecha_devolucion_estimada_formateada,
        'fecha_devolucion_real_formateada' => $prestamo->fecha_devolucion_real_formateada,

        'estado' => $prestamo->estado,
        'motivo_solicitud' => $prestamo->motivo_solicitud,
        'notas_archivero' => $prestamo->notas_archivero,
        'motivo_rechazo' => $prestamo->motivo_rechazo,
        'es_verbal' => $prestamo->es_verbal,

        'carpeta' => $prestamo->carpeta ? [
            'id' => $prestamo->carpeta->id,
            'letra' => $prestamo->carpeta->letra,
            'codigo' => $prestamo->carpeta->codigo,
            'nombrecompleto' => $prestamo->carpeta->nombrecompleto,
        ] : null,
        
        // ✅ AGREGAR ESTOS TRES:
        'solicitante' => $prestamo->solicitante ? [
            'id' => $prestamo->solicitante->id,
            'name' => $prestamo->solicitante->name,  // Asegúrate que el campo sea 'name' o 'nombre'
        ] : null,
        
        'archivero' => $prestamo->archivero ? [
            'id' => $prestamo->archivero->id,
            'name' => $prestamo->archivero->name,
        ] : null,
        
        'usuario_registrador' => $prestamo->usuarioRegistrador ? [
            'id' => $prestamo->usuarioRegistrador->id,
            'name' => $prestamo->usuarioRegistrador->name,
        ] : null,
    ];
});

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Crear nuevo préstamo (solicitud)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carpeta_type' => '',
            'carpeta_id' => 'required|integer',
            //'solicitante_id' => 'required|exists:users,id',
            'fecha_solicitud' => 'required|date',
            'fecha_devolucion_estimada' => 'nullable|date|after_or_equal:fecha_solicitud',
            'motivo_solicitud' => 'nullable|string|max:1000',
            'es_verbal' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }



        // Verificar si ya tiene un préstamo activo
        $prestamoActivo = Prestamo::where('carpeta_type', $request->carpeta_type)
            ->where('carpeta_id', $request->carpeta_id)
            ->whereIn('estado', ['pendiente', 'aprobado', 'prestado'])
            ->exists();

        if ($prestamoActivo) {
            return response()->json([
                'success' => false,
                'message' => 'Esta carpeta ya tiene un préstamo activo'
            ], 400);
        }

        $prestamo = Prestamo::create([
            'carpeta_type' => $request->carpeta_type,
            'carpeta_id' => $request->carpeta_id,
            'solicitante_id' => Auth::id(),
            'user_id' => Auth::id(),
            'fecha_solicitud' => $request->fecha_solicitud,
            'fecha_devolucion_estimada' => $request->fecha_devolucion_estimada,
            'motivo_solicitud' => $request->motivo_solicitud,
            'es_verbal' => $request->es_verbal ?? false,
            'estado' => 'pendiente'
        ]);

        // Cargar relaciones para la respuesta
        $prestamo->load(['carpeta', 'solicitante', 'usuarioRegistrador']);

        // Enviar notificación a todos los usuarios con rol 'archivo' (o 'admin')
        $usuariosArchivo = User::role('archivo')->get(); // si usas spatie/laravel-permission
        foreach ($usuariosArchivo as $usuario) {
            $usuario->notify(new NuevaSolicitudPrestamo($prestamo));
        }
        // También a admins si quieres
        //$admins = User::role('admin')->get();
        //foreach ($admins as $admin) {
        //    $admin->notify(new NuevaSolicitudPrestamo($prestamo));
        //}

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de préstamo creada exitosamente',
            'data' => $prestamo
        ], 201);
    }

    /**
     * Mostrar detalles de un préstamo
     */
/**
 * Ver detalles de una profesión
 */
public function show(Profesion $profesion)
{
    // Cargar las relaciones necesarias
    $profesion->load(['persona', 'carrera.areaConocimiento', 'carrera.nivelAcademico']);
    
    // Verificar si la persona existe, si no, mostrar advertencia
    if (!$profesion->persona) {
        return redirect()->route('profesion.index')
            ->with('warning', 'La persona asociada a esta profesión ya no existe en el sistema.');
    }
    
    return view('admin.profesion.show', compact('profesion'));
}

    /**
     * Actualizar préstamo
     */
    public function update(Request $request, $id)
    {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        // Solo permitir actualización si está pendiente o aprobado
        if (!in_array($prestamo->estado, ['pendiente', 'aprobado'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar un préstamo en estado ' . $prestamo->estado
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'fecha_devolucion_estimada' => 'nullable|date|after_or_equal:fecha_solicitud',
            'motivo_solicitud' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $prestamo->update($request->only([
            'fecha_devolucion_estimada',
            'motivo_solicitud'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Préstamo actualizado exitosamente',
            'data' => $prestamo->fresh(['carpeta', 'solicitante'])
        ]);
    }

    /**
     * Aprobar préstamo
     */
 /**
 * Aprobar préstamo
 */
public function aprobar(Request $request, $id)
{
    try {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        if ($prestamo->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden aprobar préstamos pendientes'
            ], 400);
        }

        $prestamo->update([
            'estado' => 'aprobado',
            'archivero_id' => Auth::id(),
            'notas_archivero' => $request->notas_archivero
        ]);
        $prestamo->solicitante->notify(new PrestamoAprobado($prestamo));

        return response()->json([
            'success' => true,
            'message' => 'Préstamo aprobado exitosamente',
            'data' => $prestamo
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al aprobar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Rechazar préstamo
 */
public function rechazar(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'motivo_rechazo' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El motivo del rechazo es obligatorio',
                'errors' => $validator->errors()
            ], 422);
        }

        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        if ($prestamo->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden rechazar préstamos pendientes'
            ], 400);
        }

        $prestamo->update([
            'estado' => 'rechazado',
            'archivero_id' => Auth::id(),
            'motivo_rechazo' => $request->motivo_rechazo,
            'notas_archivero' => $request->notas_archivero
        ]);

        $prestamo->solicitante->notify(new PrestamoRechazado($prestamo));
        return response()->json([
            'success' => true,
            'message' => 'Préstamo rechazado',
            'data' => $prestamo
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al rechazar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Marcar como entregado
 */
public function entregar(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'fecha_prestamo' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha de préstamo es obligatoria',
                'errors' => $validator->errors()
            ], 422);
        }

        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        if (!in_array($prestamo->estado, ['aprobado', 'pendiente'])) {
            return response()->json([
                'success' => false,
                'message' => 'El préstamo debe estar aprobado para entregarlo'
            ], 400);
        }

        $prestamo->update([
            'estado' => 'prestado',
            'fecha_prestamo' => $request->fecha_prestamo,
            'archivero_id' => $prestamo->archivero_id ?? Auth::id(),
            'notas_archivero' => $request->notas_archivero
        ]);
        $prestamo->solicitante->notify(new PrestamoEntregado($prestamo));
        return response()->json([
            'success' => true,
            'message' => 'Préstamo marcado como entregado',
            'data' => $prestamo
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al marcar entregado: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Marcar como devuelto
 */
public function devolver(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'fecha_devolucion' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha de devolución es obligatoria',
                'errors' => $validator->errors()
            ], 422);
        }

        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        if (!in_array($prestamo->estado, ['prestado', 'vencido'])) {
            return response()->json([
                'success' => false,
                'message' => 'El préstamo debe estar prestado o vencido para devolverlo'
            ], 400);
        }

        $prestamo->update([
            'estado' => 'devuelto',
            'fecha_devolucion_real' => $request->fecha_devolucion,
            'notas_archivero' => $request->observaciones_devolucion
        ]);
        $prestamo->solicitante->notify(new PrestamoDevuelto($prestamo));

        return response()->json([
            'success' => true,
            'message' => 'Préstamo marcado como devuelto',
            'data' => $prestamo
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al marcar devuelto: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Cancelar préstamo (solo si está pendiente)
     */
    public function cancelar($id)
    {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        if ($prestamo->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar préstamos pendientes'
            ], 400);
        }

        $prestamo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Préstamo cancelado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de préstamos
     */
    public function estadisticas()
    {
        $this->actualizarVencidos();

        $estadisticas = [
            'total' => Prestamo::count(),
            'por_estado' => Prestamo::selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'prestamos_activos' => Prestamo::whereIn('estado', ['aprobado', 'prestado'])->count(),
            'vencidos' => Prestamo::where('estado', 'vencido')->count(),
            'pendientes_aprobacion' => Prestamo::where('estado', 'pendiente')->count(),
            'prestamos_verbales' => Prestamo::where('es_verbal', true)->count(),
            'devoluciones_hoy' => Prestamo::whereDate('fecha_devolucion_estimada', Carbon::today())
                ->whereIn('estado', ['prestado', 'aprobado'])
                ->count(),
            'top_solicitantes' => Prestamo::selectRaw('solicitante_id, count(*) as total')
                ->with('solicitante:id,name')
                ->groupBy('solicitante_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'solicitante' => $item->solicitante->name,
                        'total' => $item->total
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Actualizar préstamos vencidos
     */
    private function actualizarVencidos()
    {
        Prestamo::where('estado', 'prestado')
            ->whereNotNull('fecha_devolucion_estimada')
            ->whereDate('fecha_devolucion_estimada', '<', Carbon::today())
            ->update(['estado' => 'vencido']);
    }

    /**
     * Reporte de préstamos por rango de fechas
     */
    public function reporte(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $prestamos = Prestamo::with(['carpeta', 'solicitante', 'archivero'])
            ->whereBetween('fecha_solicitud', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        $reporte = [
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin
            ],
            'total_prestamos' => $prestamos->count(),
            'por_estado' => $prestamos->groupBy('estado')->map->count(),
            'por_tipo_carpeta' => $prestamos->groupBy('carpeta_type')->map->count(),
            'detalle' => $prestamos
        ];

        return response()->json([
            'success' => true,
            'data' => $reporte
        ]);
    }
    /**
 * Vista de gestión de préstamos (para administradores)
 */
public function indexGestion()
{
    // Estadísticas
    $stats = [
        'pendientes' => Prestamo::where('estado', 'pendiente')->count(),
        'aprobados' => Prestamo::where('estado', 'aprobado')->count(),
        'prestados' => Prestamo::where('estado', 'prestado')->count(),
        'devueltos' => Prestamo::where('estado', 'devuelto')->count(),
        'vencidos' => Prestamo::where('estado', 'vencido')->count(),
        'rechazados' => Prestamo::where('estado', 'rechazado')->count(),
    ];

    // Préstamos pendientes
    $pendientes = Prestamo::with(['carpeta', 'solicitante'])
        ->where('estado', 'pendiente')
        ->orderBy('created_at', 'desc')
        ->get();

    // Préstamos activos (aprobados y prestados)
    $activos = Prestamo::with(['carpeta', 'solicitante'])
        ->whereIn('estado', ['aprobado', 'prestado'])
        ->orderBy('fecha_prestamo', 'desc')
        ->get();

    // Historial (devueltos, vencidos, rechazados)
    $historial = Prestamo::with(['carpeta', 'solicitante'])
        ->whereIn('estado', ['devuelto', 'vencido', 'rechazado'])
        ->orderBy('updated_at', 'desc')
        ->limit(50)
        ->get();

    return view('prestamos.gestion', compact('stats', 'pendientes', 'activos', 'historial'));
}



}


// --- Módulo Productos ---
    Route::middleware('permission:productos.crear')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:productos.ver')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:productos.editar')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:productos.eliminar')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['destroy']);
    });