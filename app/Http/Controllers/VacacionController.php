<?php

// app/Http/Controllers/VacacionController.php
namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Empleado;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Http\Controllers\Controller;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\Salida;
use Illuminate\Support\Facades\DB;
use App\Models\Puesto;
use App\Models\Historial;
use App\Models\UnidadOrganizacional;
use App\Models\Gestion;
use App\Models\Feriado;
use App\Models\TipoSalida;
use Illuminate\Support\Facades\Auth;


class VacacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $vacaciones = Vacacion::with('persona')
            ->when($buscar, function ($query) use ($buscar) {
                $query->whereHas('persona', function ($q) use ($buscar) {
                    $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$buscar%"])
                    ->orWhere('nombre', 'like', "%$buscar%")
                    ->orWhere('apellidoPat', 'like', "%$buscar%")
                    ->orWhere('apellidoMat', 'like', "%$buscar%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.vacaciones.index', compact('vacaciones', 'buscar'));

    }


    public function create()
    {
        $empleados = Persona::where('estado', 1)->get();
        return view('admin.vacaciones.create', compact('empleados'));
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'idPersona' => 'required|exists:persona,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ]);

    $inicio = Carbon::parse($validated['fecha_inicio']);
    $fin = Carbon::parse($validated['fecha_fin']);

    // Lista de feriados (puedes mantener esto en base de datos o archivo si crece)
    $feriados = [
        '2025-01-01', // Año Nuevo
        '2025-03-03', // Carnaval Lunes
        '2025-03-04', // Carnaval Martes
        '2025-04-18', // Viernes Santo
        '2025-05-01', // Día del Trabajador
        '2025-06-21', // Año Nuevo Andino
        '2025-08-06', // Independencia Bolivia
        '2025-11-02', // Todos Santos
        '2025-12-25', // Navidad
        // Agrega otros feriados nacionales o regionales
    ];

    $periodo = CarbonPeriod::create($inicio, $fin);

    $diasHabiles = collect($periodo)->filter(function ($date) use ($feriados) {
        return !$date->isWeekend() && !in_array($date->format('Y-m-d'), $feriados);
    })->count();

    Vacacion::create([
        'idPersona' => $validated['idPersona'],
        'fecha_inicio' => $validated['fecha_inicio'],
        'fecha_fin' => $validated['fecha_fin'],
        'dias_tomados' => $diasHabiles
    ]);

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones creada correctamente');

}


    public function aprobar(Vacacion $vacacion)
    {
        $vacacion->update(['estado' => 'aprobado']);
        return back()->with('success', 'Vacaciones aprobadas');
    }

    public function rechazar(Vacacion $vacacion)
    {
        $vacacion->update(['estado' => 'rechazado']);
        return back()->with('success', 'Vacaciones rechazadas');
    }

    // Otros métodos del controlador...----------------------------------------------------
        /**
     * Dashboard de vacaciones para RRHH
     */
    public function dashboard(Request $request)
    {
        $empleados = Persona::where('estado', true);

        // Filtrar por área si se especifica
        if ($request->filled('area_id')) {
            $empleados->where('area_id', $request->area_id);
        }

        // Empleados de vacaciones hoy
        $enVacacionesHoy = $this->getEmpleadosEnVacaciones($request->fecha ?? today());

        // Próximas vacaciones (solicitudes aprobadas)
        $proximasVacaciones = Salida::where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '>=', today())
            ->with(['persona', 'tipoSalida'])
            ->orderBy('fechasal')
            ->limit(10)
            ->get();

        // Empleados con saldo bajo (menos de 5 días)
        $saldoBajo = VacacionPeriodo::select('persona_id', DB::raw('SUM(saldo_disponible) as total_saldo'))
            ->where('estado', 'activo')
            ->groupBy('persona_id')
            ->having('total_saldo', '<', 5)
            ->with('persona')
            ->get();

        // Estadísticas rápidas
        $estadisticas = [
            'total_empleados' => Persona::where('estado', true)->count(),
            'en_vacaciones' => $enVacacionesHoy->count(),
            'sin_saldo' => VacacionPeriodo::where('estado', 'activo')
                ->where('saldo_disponible', 0)
                ->distinct('persona_id')
                ->count('persona_id'),
            'con_saldo_vencido' => VacacionPeriodo::where('periodo_vencido', true)
                ->where('dias_vencidos', '>', 0)
                ->distinct('persona_id')
                ->count('persona_id'),
        ];

        // Gráfico: Vacaciones por mes
        $vacacionesPorMes = Salida::where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->whereYear('fechasal', today()->year)
            ->select(DB::raw('MONTH(fechasal) as mes'), DB::raw('COUNT(*) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('admin.vacaciones.dashboard', compact(
            'empleados',
            'enVacacionesHoy',
            'proximasVacaciones',
            'saldoBajo',
            'estadisticas',
            'vacacionesPorMes'
        ));
    }

    /**
     * Reporte detallado de vacaciones por empleado
     */
    public function reporteEmpleado(Request $request, ?int $personaId = null)
    {
        // Si no se especifica empleado, mostrar formulario de búsqueda
        if (!$personaId) {
            return view('vacaciones.rrhh.reporte-empleado', [
                'empleados' => Persona::where('estado', true)->orderBy('apellidos')->get()
            ]);
        }

        $persona = Persona::with(['vacacionPeriodos', 'vacacionMovimientos'])->findOrFail($personaId);

        // Períodos de vacación
        $periodos = VacacionPeriodo::where('persona_id', $personaId)
            ->with(['movimientos' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('numero_periodo', 'desc')
            ->get();

        // Resumen por períodos
        $resumen = [
            'total_asignado' => $periodos->sum('dias_asignados'),
            'total_usado' => $periodos->sum('dias_usados'),
            'total_vencido' => $periodos->sum('dias_vencidos'),
            'total_arrastre' => $periodos->sum('dias_arrastre'),
            'saldo_actual' => $periodos->filter(function($p) {
                return $p->estado === 'activo';
            })->sum('saldo_disponible'),
        ];

        // Historial de solicitudes
        $solicitudes = Salida::where('persona_id', $personaId)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->with(['tipoSalida', 'movimientosVacacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Movimientos del kardex
        $movimientos = VacacionMovimiento::whereHas('periodo', function($q) use ($personaId) {
                $q->where('persona_id', $personaId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->with('periodo')
            ->get();

        return view('vacaciones.rrhh.reporte-empleado', compact(
            'persona',
            'periodos',
            'resumen',
            'solicitudes',
            'movimientos'
        ));
    }

    /**
     * Lista de empleados que están de vacaciones en una fecha específica
     */
    public function empleadosEnVacaciones(Request $request)
    {
        $fecha = $request->fecha ? Carbon::parse($request->fecha) : today();

        $empleados = $this->getEmpleadosEnVacaciones($fecha);

        return view('vacaciones.rrhh.en-vacaciones', [
            'empleados' => $empleados,
            'fecha' => $fecha
        ]);
    }

    /**
     * Historial de vacaciones para un empleado (vista empleado)
     */
    public function miHistorial(Request $request)
    {
        $persona = auth()->user()->persona;

        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró su información de empleado.');
        }

        $periodos = VacacionPeriodo::where('persona_id', $persona->id)
            ->with(['movimientos' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('numero_periodo', 'desc')
            ->get();

        $resumen = [
            'total_asignado' => $periodos->sum('dias_asignados'),
            'total_usado'    => $periodos->sum('dias_usados'),
            'total_vencido'  => $periodos->sum('dias_vencidos'),
            'total_arrastre' => $periodos->sum('dias_arrastre'),
            'saldo_actual'   => $periodos->filter(function($p) {
                return $p->estado === 'activo';
            })->sum('saldo_disponible'),
        ];

        $solicitudes = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->with(['tipoSalida', 'movimientosVacacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        $movimientos = VacacionMovimiento::whereHas('periodo', function($q) use ($persona) {
                $q->where('persona_id', $persona->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->with('periodo')
            ->get();

        $periodosActivos = $periodos->filter(function($p) {
            return $p->estado === 'activo' && $p->saldo_disponible > 0;
        });

        $gestion = Gestion::where('estado', 'Habilitado')->first();
        $feriado = Feriado::where('gestion_id', $gestion->id ?? 0)->get();

        // NUEVO: array de feriados para JavaScript
        $feriadosArray = $feriado->pluck('fechaf')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d'))
            ->toArray();

        $tipoSal = TipoSalida::where('usa_tabla_antiguedad', true)->get();

        return view('empleado.vacaciones.mi-historial', compact(
            'periodos',
            'resumen',
            'solicitudes',
            'movimientos',
            'periodosActivos',
            'feriado',
            'tipoSal',
            'feriadosArray'   // <--- agregar
        ));
    }

    /**
     * Obtener empleados en vacaciones para una fecha
     */
    protected function getEmpleadosEnVacaciones($fecha)
    {
        $fecha = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);
        $fechaStr = $fecha->format('Y-m-d');

        return Persona::whereHas('salidas', function($q) use ($fechaStr) {
            $q->where('tiposalida_id', function($sub) {
                    $sub->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
                })
                ->where('estado', 'aprobado')
                ->where('fechasal', '<=', $fechaStr)
                ->where('fecharet', '>=', $fechaStr);
        })
        ->with(['salidas' => function($q) use ($fechaStr) {
            $q->where('tiposalida_id', function($sub) {
                    $sub->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
                })
                ->where('estado', 'aprobado')
                ->where('fechasal', '<=', $fechaStr)
                ->where('fecharet', '>=', $fechaStr)
                ->orderBy('fechasal');
        }])
        ->get();
    }

       /**
     * Lista de empleados con su situación de vacaciones
     */
    public function indexVacion(Request $request)
    {
        $query = Persona::where('estado', true)
            ->with([
                'vacacionPeriodos' => function($q) {
                    $q->where('estado', 'activo')
                      ->orderBy('numero_periodo', 'desc');
                },
                'historials' => function($q) {
                    $q->where('estado', 'activo')
                      ->with(['puesto', 'puesto.unidadOrganizacional']);
                }
            ]);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoMat', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por unidad organizacional
        if ($request->filled('unidad_id')) {
            $query->whereHas('historials', function($q) use ($request) {
                $q->where('estado', 'activo')
                  ->whereHas('puesto', function($sub) use ($request) {
                      $sub->where('idUnidadOrganizacional', $request->unidad_id);
                  });
            });
        }

        // Filtro por estado de vacaciones
        if ($request->filled('estado_vacacion')) {
            switch ($request->estado_vacacion) {
                case 'con_saldo':
                    $query->whereHas('vacacionPeriodos', function($q) {
                        $q->where('estado', 'activo')
                          ->where('saldo_disponible', '>', 0);
                    });
                    break;
                case 'sin_saldo':
                    $query->whereDoesntHave('vacacionPeriodos', function($q) {
                        $q->where('estado', 'activo')
                          ->where('saldo_disponible', '>', 0);
                    });
                    break;
                case 'vencidos':
                    $query->whereHas('vacacionPeriodos', function($q) {
                        $q->where('periodo_vencido', true);
                    });
                    break;
                case 'en_vacaciones':
                    $query->whereHas('salidas', function($q) {
                        $q->where('tiposalida_id', function($sub) {
                                $sub->select('id')->from('tiposalidas')
                                    ->where('usa_tabla_antiguedad', true);
                            })
                            ->where('estado', 'aprobado')
                            ->where('fechasal', '<=', now())
                            ->where('fecharet', '>=', now());
                    });
                    break;
            }
        }

        $empleados = $query->paginate(100);

        // Obtener todas las unidades organizacionales en jerarquía
        $unidades = $this->getUnidadesJerarquicas();

        // Estados para el filtro
        $estadosFiltro = [
            'todos' => 'Todos',
            'con_saldo' => 'Con Saldo Disponible',
            'sin_saldo' => 'Sin Saldo',
            'vencidos' => 'Con Días Vencidos',
            'en_vacaciones' => 'En Vacaciones Actualmente'
        ];

        return view('admin.vacaciones.index-lista', compact(
            'empleados',
            'unidades',
            'estadosFiltro',
            'request'
        ));
    }

    /**
     * Ver detalle completo de vacaciones de un empleado
     */
    public function showVacacion($id)
    {
        $persona = Persona::with([
            'cas' => function($q) {
                $q->latest();
            },
            'historials' => function($q) {
                $q->where('estado', 'activo')
                  ->with(['puesto', 'puesto.unidadOrganizacional']);
            }
        ])->findOrFail($id);

        // Obtener puesto y unidad actual
        $historialActual = $persona->historials->first();
        $puestoActual = $historialActual ? $historialActual->puesto : null;
        $unidadActual = $puestoActual ? $puestoActual->unidadOrganizacional : null;

        // 1. Períodos de vacación con sus movimientos
        $periodos = VacacionPeriodo::where('persona_id', $id)
            ->with(['movimientos' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('numero_periodo', 'desc')
            ->get();

        // 2. Resumen general
        $resumen = [
            'total_asignado' => $periodos->sum('dias_asignados'),
            'total_usado' => $periodos->sum('dias_usados'),
            'total_vencido' => $periodos->sum('dias_vencidos'),
            'total_arrastre' => $periodos->sum('dias_arrastre'),
            'saldo_actual' => $periodos->filter(function($p) {
                return $p->estado === 'activo';
            })->sum('saldo_disponible'),
            'periodos_activos' => $periodos->where('estado', 'activo')->count(),
            'periodos_vencidos' => $periodos->where('estado', 'vencido')->count(),
        ];

        // 3. Todas las solicitudes de vacación
        $solicitudes = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->with(['tipoSalida', 'movimientosVacacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Kardex completo
        $movimientos = \App\Models\VacacionMovimiento::whereHas('periodo', function($q) use ($id) {
                $q->where('persona_id', $id);
            })
            ->with(['periodo', 'salida'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // 5. Estadísticas por año
        $estadisticasAnuales = $periodos->groupBy(function($p) {
            return \Carbon\Carbon::parse($p->fecha_habilitacion)->year;
        })->map(function($grupo) {
            return [
                'asignados' => $grupo->sum('dias_asignados'),
                'usados' => $grupo->sum('dias_usados'),
                'vencidos' => $grupo->sum('dias_vencidos'),
            ];
        });

        // 6. Datos de CAS
        $casActual = $persona->cas()->latest()->first();

        // 7. Verificar si está de vacaciones actualmente
        $enVacaciones = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '<=', now())
            ->where('fecharet', '>=', now())
            ->with(['tipoSalida'])
            ->first();

        // 8. Próximas vacaciones programadas
        $proximasVacaciones = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '>', now())
            ->orderBy('fechasal')
            ->first();

        return view('admin.vacaciones.show-empleado', compact(
            'persona',
            'periodos',
            'resumen',
            'solicitudes',
            'movimientos',
            'estadisticasAnuales',
            'casActual',
            'enVacaciones',
            'proximasVacaciones',
            'puestoActual',
            'unidadActual'
        ));
    }

    /**
     * Obtener unidades organizacionales en formato jerárquico
     */
    private function getUnidadesJerarquicas()
    {
        // Obtener todas las unidades con su nivel jerárquico
        $unidades = UnidadOrganizacional::where('estado', true)
            ->get()
            ->map(function($unidad) {
                // Calcular el nivel de profundidad (0 = raíz)
                $nivel = $this->calcularNivelUnidad($unidad);
                $unidad->nivel = $nivel;
                return $unidad;
            })
            ->sortBy(function($unidad) {
                return $unidad->nivel . '-' . $unidad->denominacion;
            });

        return $unidades;
    }

    /**
     * Calcular el nivel de profundidad de una unidad
     */
    private function calcularNivelUnidad($unidad, $nivel = 0)
    {
        if (!$unidad->idPadre) {
            return $nivel;
        }

        $padre = UnidadOrganizacional::find($unidad->idPadre);
        if ($padre) {
            return $this->calcularNivelUnidad($padre, $nivel + 1);
        }

        return $nivel;
    }

    /**
     * Exportar historial completo a PDF
     */
    public function exportarPDF($id)
    {
        $persona = Persona::with([
            'historials' => function($q) {
                $q->where('estado', 'activo')
                  ->with(['puesto', 'puesto.unidadOrganizacional']);
            }
        ])->findOrFail($id);

        $periodos = VacacionPeriodo::where('persona_id', $id)
            ->with('movimientos')
            ->orderBy('numero_periodo', 'desc')
            ->get();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.vacaciones.pdf-historial', compact('persona', 'periodos'));
        return $pdf->download("historial_vacaciones_{$persona->ci}.pdf");
    }

    /**
     * Ver detalle completo de vacaciones de un empleado
     */
    public function show($id)
    {
        $persona = Persona::with([
            'area',
            'cas' => function($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        // 1. Períodos de vacación con sus movimientos
        $periodos = VacacionPeriodo::where('persona_id', $id)
            ->with(['movimientos' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('numero_periodo', 'desc')
            ->get();

        // 2. Resumen general
        $resumen = [
            'total_asignado' => $periodos->sum('dias_asignados'),
            'total_usado' => $periodos->sum('dias_usados'),
            'total_vencido' => $periodos->sum('dias_vencidos'),
            'total_arrastre' => $periodos->sum('dias_arrastre'),
            'saldo_actual' => $periodos->filter(function($p) {
                return $p->estado === 'activo';
            })->sum('saldo_disponible'),
            'periodos_activos' => $periodos->where('estado', 'activo')->count(),
            'periodos_vencidos' => $periodos->where('estado', 'vencido')->count(),
        ];

        // 3. Todas las solicitudes de vacación
        $solicitudes = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->with(['tipoSalida', 'movimientosVacacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Kardex completo (todos los movimientos)
        $movimientos = VacacionMovimiento::whereHas('periodo', function($q) use ($id) {
                $q->where('persona_id', $id);
            })
            ->with(['periodo', 'salida'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // 5. Estadísticas por año
        $estadisticasAnuales = $periodos->groupBy(function($p) {
            return Carbon::parse($p->fecha_habilitacion)->year;
        })->map(function($grupo) {
            return [
                'asignados' => $grupo->sum('dias_asignados'),
                'usados' => $grupo->sum('dias_usados'),
                'vencidos' => $grupo->sum('dias_vencidos'),
            ];
        });

        // 6. Datos de CAS si existe
        $casActual = $persona->cas()->latest()->first();

        // 7. Verificar si está de vacaciones actualmente
        $enVacaciones = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '<=', now())
            ->where('fecharet', '>=', now())
            ->with(['tipoSalida'])
            ->first();

        // 8. Próximas vacaciones programadas
        $proximasVacaciones = Salida::where('persona_id', $id)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '>', now())
            ->orderBy('fechasal')
            ->first();

        return view('vacaciones.admin.show', compact(
            'persona',
            'periodos',
            'resumen',
            'solicitudes',
            'movimientos',
            'estadisticasAnuales',
            'casActual',
            'enVacaciones',
            'proximasVacaciones'
        ));
    }

    /**
     * Exportar historial completo de un empleado a PDF
     */


    /**
     * AJAX: Obtener saldo actual de un empleado
     */
    public function getSaldo($id)
    {
        $persona = Persona::findOrFail($id);
        $saldo = $persona->vacacionPeriodos()
            ->where('estado', 'activo')
            ->sum('saldo_disponible');

        $periodos = $persona->vacacionPeriodos()
            ->where('estado', 'activo')
            ->where('saldo_disponible', '>', 0)
            ->get(['id', 'numero_periodo', 'saldo_disponible']);

        return response()->json([
            'saldo_total' => $saldo,
            'periodos' => $periodos
        ]);
    }
    /**
     * Exportar reporte a Excel (para RRHH)
     */
    public function exportarReporte(Request $request)
    {
        // Implementar con Maatwebsite\Excel
        // Generar excel con todos los empleados y su situación de vacaciones
    }
}
