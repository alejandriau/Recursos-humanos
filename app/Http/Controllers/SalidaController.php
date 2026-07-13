<?php

namespace App\Http\Controllers;

use App\Models\BeneficioPeriodo;
use App\Models\Feriado;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\Salida;
use App\Models\Tiposalida;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Configuration\IniSettingCollectionIterator;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function buscarSuperio(Request $request){
        $datos="Demetrio";
        $personal = Personal::where("nombre","like", "%$datos%")
        ->orWhere("apellidopat","like", "%$datos%")
        ->orWhere("apellidomat","like", "%$datos%")
        ->get();
        //return response()->json($personal);
        return view("usuario.salidas.vacacion", ["personal2" => $personal2]);
    }*/
    // funcion para API buscar inmediato superior para el visto bueno
    public function buscarSuperio(Request $request)
    {
        $personal = Persona::where("nombre", "like", "%$request->buscar%")
            ->orWhere("apellidopat", "like", "%$request->buscar%")
            ->orWhere("apellidomat", "like", "%$request->buscar%")
            ->get();

        return response()->json($personal);
    }
    public function funComision()
    {

        $user = Auth::user();
        // Buscar la persona asociada a este usuario
        $persona = Persona::where('user_id', $user->id)->first();
        
        // Si no existe, podrías redirigir o manejar error
        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró información de la persona.');
        }

        $gestion = Gestion::where('estado', 'Habilitado')->get();
        $feriado = Feriado::where('gestion_id', $gestion->first()->id ?? 0)->get(); // ajusta según tu lógica
        $tipoSal = Tiposalida::get();

        return view("empleado.salidas.comision", compact('persona', 'gestion', 'feriado', 'tipoSal'));
    }
    public function funcParicular()
    {
        $gestion = Gestion::where('estado', 'Habilitado')->get();
        $idg = $gestion->first()?->id ?? 0;
        $feriado = Feriado::where('gestion_id', $idg)->get();
        $tipoSal = Tiposalida::get();

        $particular = Tiposalida::where('descripcion', 'PARTICULAR')->first();
        $hijos = collect();
        if ($particular) {
            $hijos = Tiposalida::where('id_padre', $particular->id)->get();
        }

        // Obtener la persona vinculada al usuario autenticado
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        // Si no existe persona, redirigir o mostrar error
        if (!$persona) {
            return redirect('/homeusr')->with('error', 'No se encontró su registro de persona.');
        }

        return view("empleado.salidas.salidaParticular", compact(
            "tipoSal",
            "gestion",
            "feriado",
            "hijos",
            "persona"
        ));
    }
    // ========================== REGISTRAR SALIDA PARTICULAR ==========================
    public function registrarParticular(Request $request)
    {
        // Buscar al servidor por su id (clave primaria de persona)
        $personal = Persona::find($request->idserv);
        if (!$personal) {
            return response()->json(['error' => 'Servidor público no encontrado'], 404);
        }

        $fsalida = Carbon::parse($request->fsalida);
        $fretorno = Carbon::parse($request->fretorno);

        // Validar fechas
        if ($fretorno->lt($fsalida)) {
            return response()->json(['error' => 'La fecha de retorno no puede ser menor que la de salida'], 400);
        }

        // Validar cruce de fechas
        $cruceFechas = Salida::where('persona_id', $personal->id)
            ->where('vobo', '!=', 'rechazado')
            ->where('estado', '!=', 'rechazado')
            ->where(function ($q) use ($fsalida, $fretorno) {
                $q->whereBetween('fechasal', [$fsalida, $fretorno])
                    ->orWhereBetween('fecharet', [$fsalida, $fretorno])
                    ->orWhere(function ($q2) use ($fsalida, $fretorno) {
                        $q2->where('fechasal', '<=', $fsalida)->where('fecharet', '>=', $fretorno);
                    });
            })
            ->exists();

        if ($cruceFechas) {
            return response()->json(['error' => 'Ya tiene otra salida registrada en ese rango de fechas'], 409);
        }

        // Validar cruce de horarios en misma fecha
        $cruceHorario = Salida::where('persona_id', $personal->id)
            ->where('tiposalida_id', $request->tipoSal)
            ->where('vobo', '!=', 'rechazado')
            ->where('estado', '!=', 'rechazado')
            ->whereDate('fechasal', $request->fsalida)
            ->where(function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('horasal', '<', $request->horaret)
                        ->where('horaret', '>', $request->horasal);
                });
            })
            ->exists();

        if ($cruceHorario) {
            return response()->json([
                'error' => 'Existe otra salida particular que se cruza en el mismo horario.'
            ], 422);
        }

        // Validar que no haya solicitud pendiente
        $pendiente = Salida::where('persona_id', $personal->id)
            ->where('tiposalida_id', $request->tipoSal)
            ->where('estado', 'espera')
            ->where('vobo', '!=', 'rechazado')
            ->exists();

        if ($pendiente) {
            return response()->json(['error' => 'Ya tiene una salida particular pendiente por validar'], 409);
        }

        // Validar beneficios según subtipo
        $gestion = Gestion::where('estado', 'Habilitado')->first();
        $beneficio = BeneficioPeriodo::where('persona_id', $personal->id)
            ->where('tiposalida_id', $request->tipoSal)
            ->where('gestion_id', $gestion->id)
            ->first();

        if (!$beneficio || $beneficio->cantidad <= 0) {
            return response()->json(['error' => 'No dispone de beneficios disponibles para este subtipo'], 403);
        }

        // Registrar salida
        $salida = new Salida();
        $salida->fechasal = $fsalida;
        $salida->horasal = $request->horasal;
        $salida->fecharet = $fretorno;
        $salida->horaret = $request->horaret;
        $salida->fechasol = $request->fechasol;
        $salida->cantidad = $request->cantidad ?? 1; // valor por defecto
        $salida->vobo = "pendiente";
        $salida->id_vobo = $request->idSup;
        $salida->estado = "espera";
        $salida->persona_id = $personal->id;
        $salida->tiposalida_id = $request->tipoSal;
        $salida->save();

        return response()->json(['mensaje' => 'Salida particular registrada correctamente'], 201);
    }

public function funcSalud()
{
    $gestion = Gestion::where('estado', 'Habilitado')->get();
    $idg = $gestion->first()?->id ?? 0;
    $feriado = Feriado::where('gestion_id', $idg)->get();
    $tipoSal = Tiposalida::get();

    // Obtener la persona vinculada al usuario autenticado
    $user = auth()->user();
    $persona = Persona::where('user_id', $user->id)->first();

    if (!$persona) {
        return redirect('/homeusr')->with('error', 'No se encontró su registro de persona.');
    }

    return view("empleado.salidas.salidaSalud", compact(
        "tipoSal",
        "gestion",
        "feriado",
        "persona"
    ));
}

    public function index(Request $request)
    {
        // Obtener la gestión habilitada actual
        $gestion = Gestion::where('estado', 'Habilitado')->get();
        $idg = $gestion->first()?->id ?? null;

        // Obtener feriados para la gestión actual
        $feriado = $idg ? Feriado::where('gestion_id', $idg)->get() : collect();

        // Obtener la persona autenticada
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect('/')->withErrors('No se encontró información del usuario.');
        }

        // Beneficios de vacación
        $beneficios = collect();
        if ($persona && $idg) {
            $beneficios = BeneficioPeriodo::where('persona_id', $persona->id)
                ->where('gestion_id', $idg)
                ->whereHas('tiposalida', function ($query) {
                    $query->where('descripcion', 'VACACION');
                })
                ->get();
        }

        // Todos los tipos de salida (para el select, aunque solo se usará VACACION)
        $tipoSal = Tiposalida::all();

        return view('empleado.vacaciones.index', [
            'tipoSal'    => $tipoSal,
            'gestion'    => $gestion,
            'feriado'    => $feriado,
            'beneficios' => $beneficios,
            'persona'    => $persona,
        ]);
    }

    // *********************** obtiene dias disponibles para vacaion *********************************

public function obtenerDiasDisponibles(Request $request)
{
    try {

        $gestion = Gestion::where('estado', 'Habilitado')->first();

        if (!$gestion) {
            return response()->json(['dias' => 0]);
        }

        $beneficio = BeneficioPeriodo::where('persona_id', $request->idpersona)
            ->where('gestion_id', $gestion->id)
            ->whereHas('tiposalida', function ($q) {
                $q->where('descripcion', 'VACACION');
            })
            ->first();

        $diasUsados = Salida::where('persona_id', $request->idpersona)
            ->where('estado', '!=', 'rechazado')
            ->where('vobo', '!=', 'rechazado')
            ->whereYear('fechasal', $gestion->anio)
            ->whereHas('tiposalida', function ($q) {
                $q->where('descripcion', 'VACACION');
            })
            ->sum('cantidad');

        $diasDisponibles = ($beneficio?->cantidad ?? 0) - $diasUsados;

        return response()->json([
            'dias' => max(0, $diasDisponibles)
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'error' => $e->getMessage(),
            'linea' => $e->getLine(),
            'archivo' => $e->getFile()
        ], 500);
    }
}
    // ****************** obtiene dias disponibles para salidas particulares ****************************
    public function diasDisponibles(Request $request)
    {
        $personalId = Persona::where('idservidor', $request->idservidor)->value('id');
        $beneficio = BeneficioPeriodo::where('personal_id', $personalId)
            ->where('tiposalida_id', $request->tiposalida_id)
            ->first();

        return response()->json([
            'dias' => $beneficio ? $beneficio->cantidad : 0
        ]);
    }
public function registrarVacacion(Request $request)
{
    // Validación con Rule::exists para evitar el error de tabla
    $request->validate([
        'persona_id' => ['required', Rule::exists('persona', 'id')],
        'tipoSal'    => ['required', Rule::exists('tiposalidas', 'id')],
        'fechasol'   => 'required|date',
        'fsalida'    => 'required|date',
        'fretorno'   => 'required|date|after_or_equal:fsalida',
        'totaldias'  => 'required|numeric|min:0.5',
        'idSup'      => ['required', Rule::exists('persona', 'id')],
        'observacion'=> 'nullable|string',
    ]);

    $personal = Persona::find($request->persona_id);
    if (!$personal) {
        return response()->json(['error' => 'Servidor público no encontrado'], 404);
    }

    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);

    // Verificar superposición de fechas (excluyendo rechazados)
    $existe = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('vobo', '!=', 'rechazado')
        ->where('estado', '!=', 'rechazado')
        ->where(function ($q) use ($fsalida, $fretorno) {
            $q->whereBetween('fechasal', [$fsalida, $fretorno])
              ->orWhereBetween('fecharet', [$fsalida, $fretorno])
              ->orWhere(function ($q2) use ($fsalida, $fretorno) {
                  $q2->where('fechasal', '<=', $fsalida)
                     ->where('fecharet', '>=', $fretorno);
              });
        })
        ->exists();

    if ($existe) {
        return response()->json(['error' => 'Ya existe una solicitud de vacación en ese rango de fechas'], 409);
    }

    // Verificar solicitud pendiente
    $pendiente = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('estado', 'espera')
        ->where('vobo', '!=', 'rechazado')
        ->exists();

    if ($pendiente) {
        return response()->json(['error' => 'Ya tiene una solicitud de vacación pendiente por validar'], 409);
    }

    // Obtener gestión habilitada
    $gestion = Gestion::where('estado', 'Habilitado')->first();
    if (!$gestion) {
        return response()->json(['error' => 'No hay una gestión habilitada'], 400);
    }

    // Obtener beneficio de vacación
    $beneficio = BeneficioPeriodo::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('gestion_id', $gestion->id)
        ->first();
    if (!$beneficio) {
        return response()->json(['error' => 'No tiene asignado beneficio de vacación para esta gestión']);
    }

    // ✅ Calcular días ya utilizados (aprobados o en proceso) en la misma gestión
    // Se usa el año de la gestión para filtrar (asumiendo que Gestion tiene campo 'anio')
    $anioGestion = $gestion->anio; // Si no existe, usa fechas: $gestion->fecha_inicio y $gestion->fecha_fin
    $diasUsados = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('estado', '!=', 'rechazado')
        ->where('vobo', '!=', 'rechazado')
        ->whereYear('fechasal', $anioGestion)  // ✅ Filtro por año
        ->sum('cantidad');

    $diasDisponibles = floatval($beneficio->cantidad) - $diasUsados;

    if (floatval($request->totaldias) > $diasDisponibles) {
        return response()->json([
            'error' => "No dispone de suficientes días de vacación. Disponibles: {$diasDisponibles}"
        ], 403);
    }

    // Registrar solicitud
    $salida = new Salida();
    $salida->fechasal = $fsalida;
    $salida->fecharet = $fretorno;
    $salida->cantidad = floatval($request->totaldias);
    $salida->fechasol = $request->fechasol;
    $salida->vobo = 'pendiente';
    $salida->id_vobo = $request->idSup;
    $salida->estado = 'espera';
    $salida->persona_id = $personal->id;
    $salida->tiposalida_id = $request->tipoSal;
    $salida->observacion = $request->observacion ?? null;
    $salida->save();


    return response()->json(['mensaje' => 'Solicitud de vacación registrada correctamente'], 201);
}

public function registrarComision(Request $request)
{
    // 1. Obtener usuario autenticado
    $user = Auth::user();
    $personal = Persona::where('id', $user->id)->first(); // o where('user_id', $user->id)
    if (!$personal) {
        return response()->json(['error' => 'Datos de usuario no encontrados'], 404);
    }

    // 2. Validar superior
    $superior = Persona::find($request->idSup);
    if (!$superior) {
        return response()->json(['error' => 'Superior no válido'], 404);
    }

    // 3. Convertir fechas
    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);

    // 4. Validaciones básicas
    if ($fretorno->lt($fsalida)) {
        return response()->json(['error' => 'La fecha de retorno no puede ser menor a la de salida'], 400);
    }

    if (Carbon::parse($request->fechasol)->gt($fsalida)) {
        return response()->json(['error' => 'La fecha de solicitud no puede ser posterior a la salida'], 422);
    }

    // 5. Validar días inhábiles (feriados y fines de semana)
    $feriados = Feriado::pluck('fechaf')->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))->toArray();
    foreach ([$fsalida, $fretorno] as $fecha) {
        $fechaStr = $fecha->format('Y-m-d');
        if (in_array($fechaStr, $feriados) || $fecha->isWeekend()) {
            return response()->json(['error' => "La fecha $fechaStr es inhábil"], 422);
        }
    }

    // 6. Validar cruce de horarios en misma fecha
    $cruceHorario = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('vobo', '!=', 'rechazado')
        ->where('estado', '!=', 'rechazado')
        ->whereDate('fechasal', $request->fsalida)
        ->where(function ($q) use ($request) {
            $q->where('horasal', '<', $request->horaret)
              ->where('horaret', '>', $request->horasal);
        })
        ->exists();

    if ($cruceHorario) {
        return response()->json(['error' => 'Horario se cruza con otra comisión en la misma fecha'], 422);
    }

    // 7. Validar comisión pendiente
    $pendiente = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('estado', 'espera')
        ->where('vobo', '!=', 'rechazado')
        ->exists();

    if ($pendiente) {
        return response()->json(['error' => 'Ya tiene una comisión pendiente de validación'], 409);
    }

    // 8. Transacción para guardar
    DB::beginTransaction();
    try {
        $salida = new Salida();
        $salida->fechasal = $fsalida;
        $salida->horasal = $request->horasal;
        $salida->fecharet = $fretorno;
        $salida->horaret = $request->horaret;
        $salida->fechasol = $request->fechasol;
        $salida->motivo = $request->motivo;
        $salida->vobo = 'pendiente';
        $salida->id_vobo = $request->idSup;
        $salida->estado = 'espera';
        $salida->persona_id = $personal->id; // Asegura el nombre correcto
        $salida->tiposalida_id = $request->tipoSal;
        $salida->save();

        DB::commit();
        return response()->json(['mensaje' => 'Solicitud de comisión registrada correctamente'], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al registrar: ' . $e->getMessage()], 500);
    }
}

    /* public function registarSalSalud(Request $request)
    {
        $perId = Personal::where('idservidor', $request->idserv)->first();
        $salida = new Salida();
        $salida->fechasal = $request->fsalida;
        $salida->horasal = $request->horasal;
        $salida->fecharet = $request->fretorno;
        $salida->horaret = $request->horaret;
        $salida->fechasol = $request->fechasol;
        $salida->vobo = "pendiente"; // cambia a valor aprobado si el inmediato superior acepta la solicitud
        $salida->id_vobo = $request->idSup;
        $salida->estado = "espera"; // cambia al valor validado si RRHH valida la solicitud
        $salida->personal_id = $perId->id;
        $salida->tiposalida_id = $request->tipoSal;
        $salida->save();
        return response()->json(['mensaje' => 'datos guardados correctamente'], 201);
    }*/
public function registarSalSalud(Request $request)
{
    // Buscar por ID de la tabla persona (no por idservidor)
    $personal = Persona::find($request->idserv);
    if (!$personal) {
        return response()->json(['error' => 'Servidor público no encontrado'], 404);
    }

    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);

    if ($fretorno->lt($fsalida)) {
        return response()->json(['error' => 'La fecha de retorno no puede ser menor que la de salida'], 400);
    }

    // Validar cruce de horarios (cualquier tipo de salida)
    $cruceHorarioGeneral = Salida::where('persona_id', $personal->id)
        ->where(function ($q) use ($fsalida, $fretorno, $request) {
            $q->whereDate('fechasal', $fsalida->format('Y-m-d'))
              ->orWhereDate('fecharet', $fretorno->format('Y-m-d'));
        })
        ->where('vobo', '!=', 'rechazado')
        ->where('estado', '!=', 'rechazado')
        ->where(function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('horasal', '<', $request->horaret)
                    ->where('horaret', '>', $request->horasal);
            });
        })
        ->exists();

    if ($cruceHorarioGeneral) {
        return response()->json(['error' => 'Existe otra salida registrada con cruce de horario en la misma fecha.'], 422);
    }

    // Verificar si ya tiene una solicitud de SALUD pendiente
    $pendiente = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('estado', 'espera')
        ->where('vobo', '!=', 'rechazado')
        ->exists();

    if ($pendiente) {
        return response()->json(['error' => 'Ya tiene una salida médica pendiente por validar'], 409);
    }

    // Registrar la salida
    $salida = new Salida();
    $salida->fechasal = $fsalida;
    $salida->horasal = $request->horasal;
    $salida->fecharet = $fretorno;
    $salida->horaret = $request->horaret;
    $salida->fechasol = $request->fechasol;
    $salida->vobo = "pendiente";
    $salida->id_vobo = $request->idSup;
    $salida->estado = "espera";
    $salida->persona_id = $personal->id;
    $salida->tiposalida_id = $request->tipoSal;
    $salida->save();

    return response()->json(['mensaje' => 'Salida médica registrada correctamente'], 201);
}
    /**
     * Show the form for creating a new resource.
     */
    public function reporteusr()
    {
        return view('usuario.reportesUsr.reportegral');
    }
    public function reporteusrData(Request $request)
    {
        $idserv = $request->idserv;

        $query = \App\Models\Salida::with('tiposalida')
            ->where('personal_id', function ($q) use ($idserv) {
                $q->select('id')->from('personals')->where('idservidor', $idserv);
            });

        // Filtrar por tipo de salida
        if ($request->filled('tipo')) {
            $tipo = $request->tipo;

            // Caso especial: PARTICULAR (traer padre e hijos)
            if ($tipo === 'PARTICULAR') {
                $idPadre = \App\Models\Tiposalida::where('descripcion', 'PARTICULAR')->value('id');
                $ids = \App\Models\Tiposalida::where('id_padre', $idPadre)->pluck('id')->toArray();
                $ids[] = $idPadre; // incluir el padre también

                $query->whereIn('tiposalida_id', $ids);
            } else {
                $query->whereHas('tiposalida', function ($q) use ($tipo) {
                    $q->where('descripcion', $tipo);
                });
            }
        }

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fechasal', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $salidas = $query->orderBy('fechasol', 'desc')->get();

        return response()->json(['data' => $salidas]);
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
