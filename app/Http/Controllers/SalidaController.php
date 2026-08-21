<?php

namespace App\Http\Controllers;

use App\Models\BeneficioPeriodo;
use App\Models\Feriado;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\Salida;
use App\Models\TipoSalida;
use App\Models\Historial;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Notifications\GenericNotification; // <-- AGREGAR
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use PHPUnit\TextUI\Configuration\IniSettingCollectionIterator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;

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
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró información de la persona.');
        }

        $gestion = Gestion::where('estado', 'Habilitado')->get();
        $feriado = Feriado::where('gestion_id', $gestion->first()->id ?? 0)->get();
        $tipoSal = TipoSalida::get();

        $tipoComision = TipoSalida::where('descripcion', 'COMISION')->first();

        if (!$tipoComision) {
            return redirect()->back()->with('error', 'No se configuró el tipo de salida COMISION.');
        }

        $solicitudes = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', $tipoComision->id)
            ->with(['jefe', 'rrhh'])  // ← evita N+1 en la tabla inicial
            ->orderBy('created_at', 'desc')
            ->get();

        return view("empleado.salidas.comision", compact(
            'persona', 'gestion', 'feriado', 'tipoSal', 'solicitudes'
        ));
    }
    public function eliminar($id)
    {
        $salida = Salida::findOrFail($id);
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        // Verificar que la solicitud pertenece a la persona y que el estado_jefe sea 'pendiente'
        if ($salida->persona_id != $persona->id || $salida->estado_jefe != 'pendiente') {
            return response()->json(['error' => 'No puedes eliminar esta solicitud'], 403);
        }

        $salida->delete();
        return response()->json(['mensaje' => 'Solicitud eliminada correctamente']);
    }
    public function obtenerComision($id)
    {
        $salida = Salida::findOrFail($id);
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if ($salida->persona_id != $persona->id || $salida->estado_jefe != 'pendiente') {
            return response()->json(['error' => 'No puedes editar esta solicitud'], 403);
        }

        return response()->json($salida);
    }
    public function actualizarComision(Request $request, $id)
    {
        $request->validate([
            'fsalida' => 'required|date',
            'fretorno' => 'required|date|after_or_equal:fsalida',
            'horasal' => 'required',
            'horaret' => 'required',
            'motivo' => 'required|string',
            'idSup' => 'required|exists:persona,id',
        ]);

        $salida = Salida::findOrFail($id);
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if ($salida->persona_id != $persona->id || $salida->estado_jefe != 'pendiente') {
            return response()->json(['error' => 'No puedes editar esta solicitud'], 403);
        }

        $salida->fechasal = $request->fsalida;
        $salida->fecharet = $request->fretorno;
        $salida->horasal = $request->horasal;
        $salida->horaret = $request->horaret;
        $salida->motivo = $request->motivo;
        $salida->jefe_id = $request->idSup;
        // Mantener la fecha de solicitud (no se modifica)
        $salida->save();

        return response()->json(['mensaje' => 'Solicitud actualizada correctamente']);
    }
public function misSolicitudes()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'message' => 'No existe una persona asociada al usuario.',
                'user_id' => $user->id
            ], 404);
        }

        $tipoComision = Tiposalida::where('descripcion', 'COMISION')->first();

        if (!$tipoComision) {
            return response()->json([
                'success' => false,
                'message' => 'No existe el tipo de salida COMISION.'
            ], 404);
        }

        // IMPORTANTE: with(['jefe','rrhh']) evita N+1
        $solicitudes = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', $tipoComision->id)
            ->with(['jefe', 'rrhh'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sol) {

                // Formatear fechas de aprobación si existen
                $fechaJefe = $sol->fecha_aprobacion_jefe
                    ? \Carbon\Carbon::parse($sol->fecha_aprobacion_jefe)->format('d/m/Y H:i')
                    : null;

                $fechaRRHH = $sol->fecha_aprobacion_rrhh
                    ? \Carbon\Carbon::parse($sol->fecha_aprobacion_rrhh)->format('d/m/Y H:i')
                    : null;

                return [
                    'id' => $sol->id,
                    'fechasal' => $sol->fechasal
                        ? \Carbon\Carbon::parse($sol->fechasal)->format('d-m-Y')
                        : null,
                    'horasal' => $sol->horasal,
                    'fecharet' => $sol->fecharet
                        ? \Carbon\Carbon::parse($sol->fecharet)->format('d-m-Y')
                        : null,
                    'horaret' => $sol->horaret,
                    'motivo' => Str::limit($sol->motivo ?? '', 30),

                    // Estados
                    'estado_jefe' => $sol->estado_jefe,
                    'estado_rrhh' => $sol->estado_rrhh,

                    // Fechas de aprobación formateadas
                    'fecha_aprobacion_jefe' => $fechaJefe,
                    'fecha_aprobacion_rrhh' => $fechaRRHH,

                    // Datos del jefe (planos para evitar objetos anidados en JS)
                    'jefe_nombre' => $sol->jefe?->nombre,
                    'jefe_apellido_pat' => $sol->jefe?->apellidoPat,

                    // Datos de RRHH
                    'rrhh_nombre' => $sol->rrhh?->nombre,
                    'rrhh_apellido_pat' => $sol->rrhh?->apellidoPat,

                    // Lógica de edición: solo si jefe está pendiente o sin asignar
                    'editable' => empty($sol->estado_jefe) || $sol->estado_jefe === 'pendiente',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $solicitudes
        ]);

    } catch (\Throwable $e) {

        Log::error('Error en misSolicitudes', [
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null,
        ], 500);
    }
}

public function indexParticular()
{
    // Obtener el tipo padre "SALIDA PARTICULAR"
    $particular = TipoSalida::where('descripcion', 'SALIDA PARTICULAR')->first();


    // Obtener SOLO los hijos (subtipos) usando la relación del modelo
    $tiposHijos = $particular->hijos()->get(); // <-- método hijos() definido en el modelo



    // IDs de todos los tipos (padre + hijos) para filtrar las salidas del usuario
    $todosTipos = $tiposHijos->pluck('id')->push($particular->id)->toArray();

    // Persona autenticada
    $user = Auth::user();
    $persona = Persona::where('user_id', $user->id)->first();


    // Salidas particulares de esta persona (incluye padre e hijos)
    $salidas = Salida::where('persona_id', $persona->id)
                     ->whereIn('tiposalida_id', $todosTipos)
                     ->orderBy('created_at', 'desc')
                     ->get();

    // Datos para feriados
    $gestion = Gestion::where('estado', 'Habilitado')->get();
    $idg = $gestion->first()?->id ?? 0;
    $feriado = Feriado::where('gestion_id', $idg)->get();

    // Pasamos SOLO los hijos a la vista para el select del formulario
    return view('empleado.salidas.salidaParticular', compact(
        'salidas',
        'persona',
        'tiposHijos',      // <-- esto es lo que usaremos en el select
        'gestion',
        'feriado',
        'particular'
    ));
}

    public function showParticular($id)
    {
        // Buscar el tipo de salida por ID
        $tipo = TipoSalida::find($id);

        // Si no existe, devolver error 404
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de salida no encontrado'], 404);
        }

        // Devolver los campos necesarios para el frontend
        return response()->json([
            'id'          => $tipo->id,
            'descripcion' => $tipo->descripcion,
            'sustLegal'   => $tipo->sustLegal,  // Asegúrate que el campo exista en tu tabla
            'tipo_medida' => $tipo->unidad ?? 'dias', // 'dias' o 'horas' (valor por defecto)
        ]);
    }
    // ========================== REGISTRAR SALIDA PARTICULAR ==========================
    /**
     * Registrar una salida particular
     */
public function registrarParticular(Request $request)
{
    // 1. Validar datos básicos
    $validator = Validator::make($request->all(), [
        'idSup' => 'required|exists:persona,id',
        'fsalida' => 'required|date',
        'fretorno' => 'required|date|after_or_equal:fsalida',
        'horasal' => 'required|date_format:H:i',
        'horaret' => 'required|date_format:H:i|after:horasal',
        'fechasol' => 'required|date|before_or_equal:fsalida',
        'motivo' => 'nullable|string|max:500',
        'tipoSal' => 'required|exists:tiposalidas,id',
        'cantidad' => 'nullable|numeric|min:0.1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Obtener el servidor público
    $personaId = $request->persona_id ?? Auth::user()->persona->id ?? $request->idserv;

    $personal = Persona::find($personaId);
    if (!$personal) {
        return response()->json([
            'error' => 'Servidor público no encontrado'
        ], 404);
    }

    // 3. Convertir fechas
    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);
    $fechasol = Carbon::parse($request->fechasol);

    // 4. Validar que no sea fin de semana
    $tipoSalida = TipoSalida::find($request->tipoSal);
    if ($tipoSalida && $tipoSalida->periodicidad !== 'evento') {
        if ($fsalida->isWeekend()) {
            return response()->json([
                'error' => 'La fecha de salida es fin de semana'
            ], 422);
        }

        if ($fretorno->isWeekend()) {
            return response()->json([
                'error' => 'La fecha de retorno es fin de semana'
            ], 422);
        }
    }

    // 5. Validar feriados
    $feriados = Feriado::pluck('fechaf')->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))->toArray();

    if ($tipoSalida && $tipoSalida->periodicidad !== 'evento') {
        $fechaSalidaStr = $fsalida->format('Y-m-d');
        $fechaRetornoStr = $fretorno->format('Y-m-d');

        if (in_array($fechaSalidaStr, $feriados)) {
            return response()->json([
                'error' => 'La fecha de salida es un feriado'
            ], 422);
        }

        if (in_array($fechaRetornoStr, $feriados)) {
            return response()->json([
                'error' => 'La fecha de retorno es un feriado'
            ], 422);
        }
    }

    // 6. Validar que la fecha de solicitud no sea posterior a la salida
    if ($fechasol->gt($fsalida)) {
        return response()->json([
            'error' => 'La fecha de solicitud no puede ser posterior a la fecha de salida'
        ], 422);
    }

    // 7. VALIDAR QUE TENGA SALDO DISPONIBLE
    $gestion = Gestion::where('estado', 'Habilitado')->first();
    if (!$gestion) {
        return response()->json([
            'error' => 'No hay una gestión habilitada actualmente'
        ], 422);
    }

    $beneficio = BeneficioPeriodo::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where('gestion_id', $gestion->id)
        ->where('estado', 'activo')
        ->first();

    if (!$beneficio) {
        return response()->json([
            'error' => 'No tiene este beneficio asignado para la gestión actual'
        ], 403);
    }

    // Calcular cantidad
    $cantidad = $request->cantidad ?? $this->calcularCantidad($fsalida, $fretorno, $request->horasal, $request->horaret);

    // Validar saldo
    if ($beneficio->saldo_disponible < $cantidad) {
        return response()->json([
            'error' => "No tiene saldo suficiente. Disponible: {$beneficio->saldo_disponible}, Solicitado: {$cantidad}"
        ], 403);
    }

    // 8. VALIDAR QUE NO TENGA OTRA SALIDA EN EL MISMO RANGO DE FECHAS Y HORAS
    // VERIFICAR SOLAPAMIENTO COMPLETO (mismo día o rango de días)
    $salidasExistentes = Salida::where('persona_id', $personal->id)
        ->where('estado', '!=', 'rechazado')
        ->where(function ($query) use ($fsalida, $fretorno) {
            // Caso 1: Salida existente que abarca completamente el rango solicitado
            $query->where(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '<=', $fsalida)
                  ->where('fecharet', '>=', $fretorno);
            })
            // Caso 2: Salida existente que está dentro del rango solicitado
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '>=', $fsalida)
                  ->where('fecharet', '<=', $fretorno);
            })
            // Caso 3: Solapamiento parcial (inicio dentro)
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '>=', $fsalida)
                  ->where('fechasal', '<=', $fretorno);
            })
            // Caso 4: Solapamiento parcial (fin dentro)
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fecharet', '>=', $fsalida)
                  ->where('fecharet', '<=', $fretorno);
            });
        })
        ->get();

    // 9. FILTRAR POR CRUCE DE HORARIOS EN CADA FECHA
    $fechaActual = clone $fsalida;
    $fechasConConflicto = [];

    while ($fechaActual <= $fretorno) {
        $fechaStr = $fechaActual->format('Y-m-d');

        // Verificar si hay alguna salida en esta fecha específica que cruce horarios
        foreach ($salidasExistentes as $salidaExistente) {
            $fechaSalida = Carbon::parse($salidaExistente->fechasal);
            $fechaRetornoSalida = Carbon::parse($salidaExistente->fecharet);

            // Verificar si la salida existente cubre esta fecha
            if ($fechaSalida <= $fechaActual && $fechaRetornoSalida >= $fechaActual) {
                // Verificar cruce de horarios en esta fecha
                $horaSalidaExistente = Carbon::parse($salidaExistente->horasal);
                $horaRetornoExistente = Carbon::parse($salidaExistente->horaret);
                $horaSalidaNueva = Carbon::parse($request->horasal);
                $horaRetornoNueva = Carbon::parse($request->horaret);

                // Verificar si los horarios se superponen
                if ($horaSalidaNueva < $horaRetornoExistente &&
                    $horaRetornoNueva > $horaSalidaExistente) {
                    $fechasConConflicto[] = $fechaStr;
                    break 2; // Salir de ambos bucles
                }
            }
        }

        $fechaActual->addDay();
    }

    if (!empty($fechasConConflicto)) {
        return response()->json([
            'error' => "El horario seleccionado se cruza con otra salida en la(s) fecha(s): " . implode(', ', $fechasConConflicto)
        ], 422);
    }

    // 10. VALIDAR QUE NO TENGA UNA SOLICITUD PENDIENTE DE APROBACIÓN
    $pendiente = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh'])
        ->exists();

    if ($pendiente) {
        return response()->json([
            'error' => 'Ya tiene una solicitud pendiente de aprobación para este tipo de salida'
        ], 409);
    }

    // 11. VALIDAR QUE NO TENGA VACACIONES APROBADAS EN EL MISMO RANGO
    $vacacionesEnRango = Salida::where('persona_id', $personal->id)
        ->where('estado', 'aprobado')
        ->whereHas('tipoSalida', function($q) {
            $q->where('descripcion', 'LIKE', '%VACACION%');
        })
        ->where(function($query) use ($fsalida, $fretorno) {
            $query->where('fechasal', '<=', $fretorno)
                  ->where('fecharet', '>=', $fsalida);
        })
        ->exists();

    if ($vacacionesEnRango) {
        return response()->json([
            'error' => 'Tiene vacaciones aprobadas en el rango de fechas seleccionado'
        ], 422);
    }

    // 12. Guardar en transacción
    DB::beginTransaction();
    try {
        $salida = new Salida();

        $salida->codigo = Salida::generarCodigo();
        $salida->persona_id = $personal->id;
        $salida->tiposalida_id = $request->tipoSal;
        $salida->periodo_id = $beneficio->id;
        $salida->periodo_type = 'App\\Models\\BeneficioPeriodo';
        $salida->fechasal = $fsalida;
        $salida->horasal = $request->horasal;
        $salida->fecharet = $fretorno;
        $salida->horaret = $request->horaret;
        $salida->cantidad = $cantidad;
        $salida->motivo = $request->motivo;
        $salida->fechasol = $fechasol;
        $salida->img = $request->img ?? null;

        $salida->estado_jefe = 'pendiente';
        $salida->jefe_id = $request->idSup;
        $salida->estado_rrhh = 'pendiente';
        $salida->estado = 'pendiente_jefe';

        $salida->save();

        $beneficio->cantidad_usada = $beneficio->cantidad_usada + $cantidad;
        $beneficio->saldo_disponible = $beneficio->saldo_disponible - $cantidad;

        if ($beneficio->saldo_disponible <= 0) {
            $beneficio->estado = 'agotado';
        }

        $beneficio->save();

        DB::commit();

        return response()->json([
            'mensaje' => 'Salida particular registrada correctamente',
            'data' => $salida,
            'saldo_restante' => $beneficio->saldo_disponible
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al registrar la salida: ' . $e->getMessage()
        ], 500);
    }
}
public function editParticular($id)
{
    // Cargar la salida con sus relaciones: persona, jefe y también el tipo de salida
    $salida = Salida::with(['persona', 'jefe', 'tiposalida'])->findOrFail($id);

    $user = auth()->user();
    $persona = Persona::where('user_id', $user->id)->first();
    if ($salida->persona_id != $persona->id) {
        abort(403, 'No autorizado');
    }

    // Verificar que esté pendiente para editar
    if ($salida->estado_jefe != 'pendiente' || $salida->estado_rrhh != 'pendiente') {
        return response()->json(['error' => 'No se puede editar una salida ya aprobada o rechazada'], 422);
    }

    // Añadir el sustento legal al objeto de respuesta (opcionalmente otros campos)
    $response = $salida->toArray();
    $response['sustLegal'] = $salida->tiposalida->sustLegal ?? null;

    return response()->json($response);
}
    /**
     * Actualizar una salida particular (vía AJAX)
     */
    public function updateParticular(Request $request, $id)
    {
        $salida = Salida::findOrFail($id);
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();
        if ($salida->persona_id != $persona->id) {
            abort(403);
        }
        if ($salida->estado_jefe != 'pendiente' || $salida->estado_rrhh != 'pendiente') {
            return response()->json(['error' => 'No se puede editar una salida ya aprobada o rechazada'], 422);
        }

        $validator = Validator::make($request->all(), [
            'fsalida'     => 'required|date|after_or_equal:today',
            'horasal'     => 'required|date_format:H:i',
            'fretorno'    => 'required|date|after_or_equal:fsalida',
            'horaret'     => 'required|date_format:H:i|after:horasal',
            'fechasol'    => 'required|date|before_or_equal:fsalida',
            'motivo'      => 'nullable|string|max:500',
            'idSup'       => 'required|exists:persona,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Recalcular días
        $fechaInicio = Carbon::parse($request->fsalida);
        $fechaFin    = Carbon::parse($request->fretorno);
        $dias = $fechaInicio->diffInDays($fechaFin) + 1;

        $salida->fechasal   = $request->fsalida;
        $salida->horasal    = $request->horasal;
        $salida->fecharet   = $request->fretorno;
        $salida->horaret    = $request->horaret;
        $salida->fechasol   = $request->fechasol;
        $salida->motivo     = $request->motivo;
        $salida->jefe_id    = $request->idSup;
        $salida->cantidad   = $dias;
        $salida->save();

        return response()->json(['success' => true, 'message' => 'Salida actualizada']);
    }
    /**
     * Generar PDF de boleta de salida particular
     */
    public function pdfParticular($id)
    {
        $salida = Salida::with(['persona', 'jefe', 'rrhh'])->findOrFail($id);
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();
        if ($salida->persona_id != $persona->id) {
            abort(403);
        }

        // Historial activo para cargo/unidad
        $historial = Historial::where('persona_id', $salida->persona_id)
                            ->where('estado', 'activo')
                            ->whereNull('fecha_fin')
                            ->with(['puesto.unidadOrganizacional'])
                            ->first();

        $cargo = $historial ? $historial->puesto->denominacion : 'No definido';
        $unidad = $historial ? $historial->puesto->unidadOrganizacional->denominacion : 'No definida';

        $urlVerificacion = URL::temporarySignedRoute(
            'boleta.verificar',
            now()->addYears(2),
            ['id' => $salida->id]
        );

        // Generar QR
        $qrCode = new QRCode(
            data: $urlVerificacion,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 120,
            margin: 5
        );
        $writer = new PngWriter();
        $qrBase64 = base64_encode($writer->write($qrCode)->getString());

        $codigoControl = $salida->codigo
            ? substr($salida->codigo, 0, 4) . '-' . substr($salida->codigo, 4)
            : 'SIN-CODIGO';

        $data = [
            'salida'        => $salida,
            'cargo'         => $cargo,
            'unidad'        => $unidad,
            'qrBase64'      => $qrBase64,
            'codigoControl' => $codigoControl,
        ];

        $pdf = Pdf::loadView('empleado.boletas.boleta_particular', $data)
                ->setPaper('letter')
                ->setOption('defaultFont', 'dejavu sans');

        return $pdf->download("boleta-particular-{$salida->codigo}.pdf");
    }
public function funcSalud()
{
    $gestion = Gestion::where('estado', 'Habilitado')->get();
    $idg = $gestion->first()?->id ?? 0;
    $feriado = Feriado::where('gestion_id', $idg)->get();

    // Obtener solo los tipos de salida de salud
    $tipoSal = TipoSalida::where('descripcion', 'SALUD')->get(); // o donde descripcion LIKE '%SALUD%'

    $user = auth()->user();
    $persona = Persona::where('user_id', $user->id)->first();

    if (!$persona) {
        return redirect('/dashboard')->with('error', 'No se encontró su registro de persona.');
    }

    // Obtener IDs de los tipos de salud
    $tiposSaludIds = $tipoSal->pluck('id');

    // Obtener SOLO las solicitudes cuyo tipo de salida esté en la lista de salud
    $solicitudes = Salida::where('persona_id', $persona->id)
        ->whereIn('tiposalida_id', $tiposSaludIds)
        ->with('jefe')
        ->orderBy('created_at', 'desc')
        ->get();

    return view("empleado.salidas.salidaSalud", compact(
        "tipoSal",
        "gestion",
        "feriado",
        "persona",
        "solicitudes"
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
        $tipoSal = TipoSalida::all();

        $periodos = VacacionPeriodo::where('persona_id', $persona->id)
            ->whereIn('estado', ['activo', 'pendiente'])
            ->orderBy('numero_periodo')
            ->get();

        $diasDisponibles = $periodos->sum('saldo_disponible');

        return view('empleado.vacaciones.index', [
            'tipoSal'         => $tipoSal,
            'gestion'         => $gestion,
            'feriado'         => $feriado,
            'periodos'        => $periodos,
            'diasDisponibles' => $diasDisponibles,
            'persona'         => $persona,
        ]);
    }

    // *********************** obtiene dias disponibles para vacaion *********************************

public function obtenerDiasDisponibles(Request $request)
{
    try {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        // Suma el saldo disponible de todos los períodos activos de la persona
        $diasDisponibles = VacacionPeriodo::where('persona_id', $persona->id)
            ->whereIn('estado', ['activo', 'pendiente'])
            ->sum('saldo_disponible');

        return response()->json([
            'dias' => max(0, $diasDisponibles)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => $e->getMessage(),
            'linea'   => $e->getLine(),
            'archivo' => $e->getFile()
        ], 500);
    }
}

    public function registrarVacacion(Request $request)
    {
        // Validación
        $request->validate([
            'persona_id' => 'required|exists:persona,id',
            'tipoSal' => ['required', Rule::exists('tiposalidas', 'id')],
            'fechasol' => 'required|date',
            'fsalida' => 'required|date',
            'fretorno' => 'required|date|after_or_equal:fsalida',
            'totaldias' => 'required|numeric|min:0.5',
            'idSup' => ['required', Rule::exists('persona', 'id')],
            'observacion' => 'nullable|string',
        ]);

        $personal = Persona::find($request->persona_id);
        if (!$personal) {
            return response()->json(['error' => 'Servidor público no encontrado'], 404);
        }

        $fsalida = Carbon::parse($request->fsalida);
        $fretorno = Carbon::parse($request->fretorno);

        // Verificar superposición de fechas con otras solicitudes (excepto rechazadas)
        $existe = Salida::where('persona_id', $personal->id)
            ->where('tiposalida_id', $request->tipoSal)
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
            return response()->json(['error' => 'Ya existe una solicitud en ese rango de fechas'], 409);
        }

        // Obtener gestión habilitada
        $gestion = Gestion::where('estado', 'Habilitado')->first();
        if (!$gestion) {
            return response()->json(['error' => 'No hay una gestión habilitada'], 400);
        }

        // Verificar período de vacación activo
        $periodo = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('gestion_id', $gestion->id)
            ->where('estado', 'activo')
            ->first();

        if (!$periodo) {
            return response()->json([
                'error' => 'No tiene un período de vacación activo para esta gestión. Verifique su fecha de ingreso y antigüedad.'
            ], 400);
        }

        // Verificar si tiene días disponibles
        if ($periodo->saldo_disponible < floatval($request->totaldias)) {
            return response()->json([
                'error' => "No dispone de suficientes días de vacación. Disponibles: {$periodo->saldo_disponible} días"
            ], 403);
        }

        // Crear solicitud en estado PENDIENTE_JEFE
        $salida = new Salida();
        $salida->codigo = Salida::generarCodigo();
        $salida->persona_id = $personal->id;
        $salida->tiposalida_id = $request->tipoSal;
        $salida->periodo_id = $periodo->id;
        $salida->periodo_type = 'App\Models\VacacionPeriodo';
        $salida->fechasal = $fsalida;
        $salida->fecharet = $fretorno;
        $salida->cantidad = floatval($request->totaldias);
        $salida->fechasol = $request->fechasol;
        $salida->estado = 'pendiente_jefe';
        $salida->estado_jefe = 'pendiente';
        $salida->jefe_id = $request->idSup;
        //$salida->observacion = $request->observacion ?? null;
        $salida->save();

        // Registrar movimiento en la tabla vacacion_movimientos (solo para seguimiento)
        // NOTA: Aún NO se descuentan días, solo se registra la solicitud
        VacacionMovimiento::create([
            'periodo_id' => $periodo->id,
            'tipo' => 'debito',
            'fecha' => now(),
            'fecha_inicio' => $fsalida,
            'fecha_fin' => $fretorno,
            'cantidad' => floatval($request->totaldias),
            'saldo_anterior' => $periodo->saldo_disponible,
            'saldo_posterior' => $periodo->saldo_disponible, // Aún no se descuenta
            'salida_id' => $salida->id,
            'descripcion' => 'Solicitud de vacación pendiente de aprobación',
            'registrado_por' => auth()->id(),
        ]);
        $jefe = \App\Models\User::whereHas('persona', function($q) use ($request) {
            $q->where('id', $request->idSup);
        })->first();

        if ($jefe) {
            $jefe->notify(new GenericNotification([
                'titulo' => 'Vacaciones por aprobar',
                'mensaje' => "{$personal->nombre} {$personal->apellidoPat} solicitó {$request->totaldias} días de vacación ({$fsalida->format('d/m/Y')} al {$fretorno->format('d/m/Y')})",
                'tipo' => 'vacacion_solicitud', // <-- el JS reconoce este tipo y le pone icono
                'url' => '/jefe/dashboard',     // <-- a dónde va cuando hace clic
            ]));
        }

        return response()->json([
            'mensaje' => 'Solicitud de vacación registrada correctamente',
            'salida_id' => $salida->id,
            'estado' => 'pendiente_jefe'
        ], 201);
    }
    public function editVacacion($id)
    {
        $persona = Persona::where('user_id', auth()->id())->firstOrFail();

        $solicitud = Salida::with('jefe')->findOrFail($id);

        if ($solicitud->persona_id != $persona->id) {
            abort(403, 'No autorizado');
        }

        if (!in_array($solicitud->estado, ['pendiente_jefe', 'pendiente_rrhh'])) {
            abort(403, 'Esta solicitud no se puede editar.');
        }

        return response()->json([
            'id' => $solicitud->id,
            'fechasol' => $solicitud->fechasol,
            'fechasal' => $solicitud->fechasal,
            'fecharet' => $solicitud->fecharet,
            'cantidad' => $solicitud->cantidad,
            'superior_id' => $solicitud->jefe_id,
            'superior_nombre' => $solicitud->jefe
                ? $solicitud->jefe->nombre . ' ' . $solicitud->jefe->apellidoPat
                : '',
        ]);
    }
    /**
     * Actualizar una solicitud (solo si está pendiente)
     */
public function updateVacacion(Request $request, $id)
{
    // Validación
    $request->validate([
        'fsalida'    => 'required|date',
        'fretorno'   => 'required|date|after_or_equal:fsalida',
        'totaldias'  => 'required|numeric|min:0.5',
        'idSup'      => ['required', Rule::exists('persona', 'id')],
        'observacion'=> 'nullable|string',
    ]);

    $user = auth()->user();

    // Obtener la persona asociada al usuario autenticado
    $persona = Persona::where('user_id', $user->id)->first();
    if (!$persona) {
        return response()->json(['error' => 'Servidor público no encontrado'], 404);
    }

    // Buscar la solicitud por ID y verificar que pertenezca a la persona
    $solicitud = Salida::where('id', $id)
                       ->where('persona_id', $persona->id)
                       ->firstOrFail();

    // Verificar que sea editable
    if (!in_array($solicitud->estado, ['pendiente_jefe', 'pendiente_rrhh'])) {
        return response()->json(['error' => 'No se puede editar una solicitud ya procesada.'], 422);
    }

    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);

    // Verificar superposición de fechas con otras solicitudes (excepto rechazadas y la misma)
    $existe = Salida::where('persona_id', $persona->id)
        ->where('tiposalida_id', $solicitud->tiposalida_id)
        ->where('estado', '!=', 'rechazado')
        ->where('id', '!=', $id)
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
        return response()->json(['error' => 'Ya existe otra solicitud en ese rango de fechas'], 409);
    }

    // Obtener gestión habilitada
    $gestion = Gestion::where('estado', 'Habilitado')->first();
    if (!$gestion) {
        return response()->json(['error' => 'No hay una gestión habilitada'], 400);
    }

    // Verificar período de vacación activo
    $periodo = VacacionPeriodo::where('persona_id', $persona->id)
        ->where('gestion_id', $gestion->id)
        ->where('estado', 'activo')
        ->first();

    if (!$periodo) {
        return response()->json([
            'error' => 'No tiene un período de vacación activo para esta gestión.'
        ], 400);
    }

    // Calcular saldo disponible considerando otras solicitudes pendientes (excepto la actual)
    $saldoUsadoPendiente = Salida::where('persona_id', $persona->id)
        ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh'])
        ->where('id', '!=', $id)
        ->sum('cantidad');

    $saldoDisponibleReal = $periodo->saldo_disponible - $saldoUsadoPendiente;

    if ($saldoDisponibleReal < floatval($request->totaldias)) {
        return response()->json([
            'error' => "No dispone de suficientes días de vacación. Disponibles: {$saldoDisponibleReal} días"
        ], 403);
    }

    DB::beginTransaction();
    try {
        // Actualizar la solicitud
        $solicitud->fechasal = $fsalida;
        $solicitud->fecharet = $fretorno;
        $solicitud->cantidad = floatval($request->totaldias);
        $solicitud->jefe_id = $request->idSup;
        $solicitud->observacion = $request->observacion ?? $solicitud->observacion;
        $solicitud->save();

        // Actualizar el movimiento asociado
        $movimiento = VacacionMovimiento::where('salida_id', $solicitud->id)->first();
        if ($movimiento) {
            $saldoAnterior = $periodo->saldo_disponible;
            $movimiento->fecha_inicio = $fsalida;
            $movimiento->fecha_fin = $fretorno;
            $movimiento->cantidad = floatval($request->totaldias);
            $movimiento->saldo_anterior = $saldoAnterior;
            $movimiento->saldo_posterior = $saldoAnterior; // aún no se descuenta
            $movimiento->descripcion = 'Solicitud de vacación actualizada';
            $movimiento->save();
        } else {
            // Crear movimiento si no existe
            VacacionMovimiento::create([
                'periodo_id' => $periodo->id,
                'tipo' => 'debito',
                'fecha' => now(),
                'fecha_inicio' => $fsalida,
                'fecha_fin' => $fretorno,
                'cantidad' => floatval($request->totaldias),
                'saldo_anterior' => $periodo->saldo_disponible,
                'saldo_posterior' => $periodo->saldo_disponible,
                'salida_id' => $solicitud->id,
                'descripcion' => 'Solicitud de vacación actualizada (movimiento creado)',
                'registrado_por' => auth()->id(),
            ]);
        }

        DB::commit();

        return response()->json([
            'mensaje' => 'Solicitud actualizada correctamente',
            'salida_id' => $solicitud->id,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
    }
}

    /**
     * Eliminar una solicitud (solo si está pendiente)
     */
    public function destroyVacacion($id)
    {
        $solicitud = Salida::findOrFail($id);

        // Verificar que pertenece al usuario autenticado
        if ($solicitud->persona_id !== auth()->user()->persona_id) {
            abort(403, 'No autorizado');
        }

        // Verificar que sea editable
        if (!in_array($solicitud->estado, ['pendiente_jefe', 'pendiente_rrhh'])) {
            return response()->json(['error' => 'No se puede eliminar una solicitud ya procesada.'], 422);
        }

        DB::beginTransaction();
        try {
            // Eliminar el movimiento asociado (si existe)
            VacacionMovimiento::where('salida_id', $solicitud->id)->delete();

            // Eliminar la solicitud
            $solicitud->delete();

            DB::commit();

            return response()->json(['mensaje' => 'Solicitud eliminada correctamente'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    public function registrarComision(Request $request)
    {
        // 1. Validar datos básicos
        $validator = Validator::make($request->all(), [
            'idSup' => 'required|exists:persona,id',
            'fsalida' => 'required|date',
            'fretorno' => 'required|date|after_or_equal:fsalida',
            'horasal' => 'required|date_format:H:i',
            'horaret' => 'required|date_format:H:i|after:horasal',
            'fechasol' => 'required|date|before_or_equal:fsalida',
            'motivo' => 'nullable|string|max:500',
            'tipoSal' => 'required|exists:tiposalidas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Obtener usuario autenticado
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return response()->json([
                'error' => 'Datos de usuario no encontrados'
            ], 404);
        }

        // 3. Validar superior
        $superior = Persona::find($request->idSup);
        if (!$superior) {
            return response()->json([
                'error' => 'Superior no válido'
            ], 404);
        }

        // 4. Convertir fechas
        $fsalida = Carbon::parse($request->fsalida);
        $fretorno = Carbon::parse($request->fretorno);
        $fechasol = Carbon::parse($request->fechasol);

        // 5. Validar que no sea fin de semana
        if ($fsalida->isWeekend()) {
            return response()->json([
                'error' => 'La fecha de salida es fin de semana'
            ], 422);
        }

        if ($fretorno->isWeekend()) {
            return response()->json([
                'error' => 'La fecha de retorno es fin de semana'
            ], 422);
        }

        // 6. Validar feriados
        $feriados = Feriado::pluck('fechaf')->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))->toArray();

        $fechaSalidaStr = $fsalida->format('Y-m-d');
        $fechaRetornoStr = $fretorno->format('Y-m-d');

        if (in_array($fechaSalidaStr, $feriados)) {
            return response()->json([
                'error' => 'La fecha de salida es un feriado'
            ], 422);
        }

        if (in_array($fechaRetornoStr, $feriados)) {
            return response()->json([
                'error' => 'La fecha de retorno es un feriado'
            ], 422);
        }

        // 7. Validar que la fecha de solicitud no sea posterior a la salida
        if ($fechasol->gt($fsalida)) {
            return response()->json([
                'error' => 'La fecha de solicitud no puede ser posterior a la fecha de salida'
            ], 422);
        }

        // 8. VALIDAR QUE NO TENGA OTRA COMISIÓN EN EL MISMO RANGO DE FECHAS Y HORAS
        // Esto valida cruce de fechas y horarios

        // Convertir horas a timestamp para comparación
        $horaSalida = Carbon::parse($request->horasal);
        $horaRetorno = Carbon::parse($request->horaret);

        // Buscar comisiones que se crucen en fecha y hora
        $comisionExistente = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', $request->tipoSal)
            ->where('estado', '!=', 'rechazado')
            ->where(function ($query) use ($fsalida, $fretorno, $horaSalida, $horaRetorno) {
                // Para cada día en el rango de fechas
                $fechaActual = clone $fsalida;
                while ($fechaActual <= $fretorno) {
                    $fechaStr = $fechaActual->format('Y-m-d');

                    $query->orWhere(function ($subQuery) use ($fechaStr, $horaSalida, $horaRetorno) {
                        $subQuery->whereDate('fechasal', '<=', $fechaStr)
                                 ->whereDate('fecharet', '>=', $fechaStr)
                                 ->where(function ($horarioQuery) use ($horaSalida, $horaRetorno) {
                                     // Validar cruce de horarios en esa fecha
                                     $horarioQuery->where(function ($q) use ($horaSalida, $horaRetorno) {
                                         // Caso 1: El nuevo horario está dentro de uno existente
                                         $q->whereTime('horasal', '<=', $horaSalida)
                                           ->whereTime('horaret', '>=', $horaSalida);
                                     })->orWhere(function ($q) use ($horaSalida, $horaRetorno) {
                                         // Caso 2: El horario existente está dentro del nuevo
                                         $q->whereTime('horasal', '>=', $horaSalida)
                                           ->whereTime('horasal', '<=', $horaRetorno);
                                     })->orWhere(function ($q) use ($horaSalida, $horaRetorno) {
                                         // Caso 3: El nuevo horario cubre completamente al existente
                                         $q->whereTime('horasal', '>=', $horaSalida)
                                           ->whereTime('horaret', '<=', $horaRetorno);
                                     })->orWhere(function ($q) use ($horaSalida, $horaRetorno) {
                                         // Caso 4: El existente cubre completamente al nuevo
                                         $q->whereTime('horasal', '<=', $horaSalida)
                                           ->whereTime('horaret', '>=', $horaRetorno);
                                     });
                                 });
                    });

                    $fechaActual->addDay();
                }
            })
            ->exists();

        if ($comisionExistente) {
            return response()->json([
                'error' => 'Ya tiene una comisión registrada que se cruza en fecha y horario con la solicitud'
            ], 422);
        }

        // 9. VALIDAR QUE NO TENGA UNA COMISIÓN PENDIENTE DE APROBACIÓN
        $pendiente = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', $request->tipoSal)
            ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh'])
            ->exists();

        if ($pendiente) {
            return response()->json([
                'error' => 'Ya tiene una comisión pendiente de aprobación'
            ], 409);
        }

        // 10. Validar que no tenga vacaciones aprobadas en el mismo rango de fechas
       /* $vacacionesEnRango = VacacionPeriodo::where('persona_id', $persona->id)
            ->where('estado', 'activo')
            ->whereHas('salidas', function($query) use ($fsalida, $fretorno) {
                $query->where('estado', 'aprobado')
                      ->where(function($q) use ($fsalida, $fretorno) {
                          $q->where('fechasal', '<=', $fretorno)
                            ->where('fecharet', '>=', $fsalida);
                      });
            })
            ->exists();

        if ($vacacionesEnRango) {
            return response()->json([
                'error' => 'Tiene vacaciones aprobadas en el rango de fechas seleccionado'
            ], 422);
        }*/

        // 11. Validar que no tenga otras salidas (beneficios) aprobadas en el mismo rango
        $otrasSalidas = Salida::where('persona_id', $persona->id)
            ->where('tiposalida_id', '!=', $request->tipoSal)
            ->where('estado', 'aprobado')
            ->where(function($query) use ($fsalida, $fretorno) {
                $query->where('fechasal', '<=', $fretorno)
                      ->where('fecharet', '>=', $fsalida);
            })
            ->exists();

        if ($otrasSalidas) {
            return response()->json([
                'error' => 'Tiene otra salida aprobada en el rango de fechas seleccionado'
            ], 422);
        }

        // 12. Calcular cantidad de días/horas
        $cantidad = $this->calcularCantidad($fsalida, $fretorno, $request->horasal, $request->horaret);

        // 13. Guardar en transacción
        DB::beginTransaction();
        try {
            $salida = new Salida();
            $salida->codigo = Salida::generarCodigo();
            $salida->persona_id = $persona->id;
            $salida->tiposalida_id = $request->tipoSal;
            $salida->periodo_id = null;
            $salida->periodo_type = null;
            $salida->fechasal = $fsalida;
            $salida->horasal = $request->horasal;
            $salida->fecharet = $fretorno;
            $salida->horaret = $request->horaret;
            $salida->cantidad = $cantidad;
            $salida->motivo = $request->motivo;
            $salida->fechasol = $fechasol;
            $salida->img = $request->img ?? null;

            // Estado de aprobación
            $salida->estado_jefe = 'pendiente';
            $salida->jefe_id = $request->idSup;
            $salida->estado_rrhh = 'pendiente';
            $salida->estado = 'pendiente_jefe';

            $salida->save();

            DB::commit();

            return response()->json([
                'mensaje' => 'Solicitud de comisión registrada correctamente',
                'data' => $salida
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al registrar la comisión: ' . $e->getMessage()
            ], 500);
        }
    }
    public function boletaComisionPdf($id)
    {
        $salida = Salida::with(['persona', 'jefe', 'rrhh'])->findOrFail($id);

        // Historial activo
        $historial = Historial::where('persona_id', $salida->persona_id)
                            ->where('estado', 'activo')
                            ->whereNull('fecha_fin')
                            ->with(['puesto.unidadOrganizacional'])
                            ->first();

        $cargo = $historial ? $historial->puesto->denominacion : 'No definido';
        $unidad = $historial ? $historial->puesto->unidadOrganizacional->denominacion : 'No definida';

        $urlVerificacion = URL::temporarySignedRoute(
            'boleta.verificar',
            now()->addYears(2),
            ['id' => $salida->id]
        );

        // --- QR CORRECTO (SIN BUILDER) ---
        $qrCode = new QrCode(
            data: $urlVerificacion,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 220,
            margin: 8
        );

        $writer = new PngWriter();
        $qrBase64 = base64_encode($writer->write($qrCode)->getString());

        $codigoControl = $salida->codigo
            ? substr($salida->codigo, 0, 4) . '-' . substr($salida->codigo, 4)
            : 'SIN-CODIGO';

        $data = [
            'salida'        => $salida,
            'cargo'         => $cargo,
            'unidad'        => $unidad,
            'qrBase64'      => $qrBase64,
            'codigoControl' => $codigoControl,
        ];

        $pdf = Pdf::loadView('empleado.boletas.comision-pdf', $data)
                ->setPaper('letter')
                ->setOption('defaultFont', 'dejavu sans');

        return $pdf->download("boleta-{$salida->codigo}.pdf");
    }
    public function boletaVacacionPdf($id)
    {
        // Cargar la salida con relaciones
        $salida = Salida::with(['persona', 'jefe', 'rrhh', 'tiposalida'])->findOrFail($id);

        // (Opcional) Verificar que sea una vacación
        // if ($salida->tiposalida->nombre !== 'Vacación') abort(404);

        // Historial activo para cargo y unidad
        $historial = Historial::where('persona_id', $salida->persona_id)
                            ->where('estado', 'activo')
                            ->whereNull('fecha_fin')
                            ->with(['puesto.unidadOrganizacional'])
                            ->first();

        $cargo = $historial ? $historial->puesto->denominacion : 'No definido';
        $unidad = $historial ? $historial->puesto->unidadOrganizacional->denominacion : 'No definida';

        // Calcular días solicitados (prioriza cantidad, si no, calcula con fechas)
        $diasSolicitados = $salida->cantidad ?? 0;
        if ($diasSolicitados == 0) {
            $inicio = \Carbon\Carbon::parse($salida->fechasal);
            $fin    = \Carbon\Carbon::parse($salida->fecharet);
            $diasSolicitados = $inicio->diffInDays($fin) + 1; // inclusivo
        }

        // URL de verificación (firma temporal)
        $urlVerificacion = URL::temporarySignedRoute(
            'boleta.verificar',
            now()->addYears(2),
            ['id' => $salida->id]
        );

        // Generar QR
        $qrCode = new QrCode(
            data: $urlVerificacion,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 220,
            margin: 8
        );
        $writer = new PngWriter();
        $qrBase64 = base64_encode($writer->write($qrCode)->getString());

        // Código de control
        $codigoControl = $salida->codigo
            ? substr($salida->codigo, 0, 4) . '-' . substr($salida->codigo, 4)
            : 'SIN-CODIGO';

        $data = [
            'salida'          => $salida,
            'cargo'           => $cargo,
            'unidad'          => $unidad,
            'diasSolicitados' => $diasSolicitados,
            'qrBase64'        => $qrBase64,
            'codigoControl'   => $codigoControl,
        ];

        //return view("empleado.boletas.vacacion-pdf", $data);
        // Generar PDF con soporte para acentos
        $pdf = Pdf::loadView('empleado.boletas.vacacion-pdf', $data)
                ->setPaper('letter')
                ->setOption('defaultFont', 'dejavu sans');

        return $pdf->download("boleta-vacacion-{$salida->codigo}.pdf");
    }

    /**
     * Calcular cantidad de días/horas
     */
    private function calcularCantidad($fsalida, $fretorno, $horasal, $horaret)
    {
        // Si es el mismo día, calcular horas
        if ($fsalida->format('Y-m-d') === $fretorno->format('Y-m-d')) {
            $inicio = Carbon::parse($horasal);
            $fin = Carbon::parse($horaret);
            $horas = $fin->diffInHours($inicio);
            return round($horas, 1);
        }

        // Si son días diferentes, contar días hábiles
        $dias = 0;
        $fecha = clone $fsalida;
        $feriados = Feriado::pluck('fechaf')->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))->toArray();

        while ($fecha <= $fretorno) {
            if (!$fecha->isWeekend() && !in_array($fecha->format('Y-m-d'), $feriados)) {
                $dias++;
            }
            $fecha->addDay();
        }

        return $dias;
    }

    /**
     * Obtener comisiones del usuario
     */
    public function misComisiones()
    {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect('/')->withErrors('No se encontró información del usuario.');
        }

        $comisiones = Salida::where('persona_id', $persona->id)
            ->whereHas('tipoSalida', function($q) {
                $q->where('descripcion', 'LIKE', '%COMISION%');
            })
            ->with(['tipoSalida', 'jefe'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('empleado.comisiones.index', compact('comisiones'));
    }

    /**
     * Buscar superiores para comisión
     */
    public function buscarSuperior(Request $request)
    {
        $query = $request->get('q');

        $personas = Persona::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('apellidoPat', 'LIKE', "%{$query}%")
            ->orWhere('apellidoMat', 'LIKE', "%{$query}%")
            ->orWhere('ci', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($personas);
    }

    /**
     * Obtener feriados para el calendario
     */
    public function getFeriados()
    {
        $gestion = Gestion::where('estado', 'Habilitado')->first();

        if (!$gestion) {
            return response()->json([]);
        }

        $feriados = Feriado::where('gestion_id', $gestion->id)
            ->select('fechaf as date', 'descripcion as title')
            ->get();

        return response()->json($feriados);
    }

    /**
     * Validar disponibilidad de fecha/hora en tiempo real
     */
    public function validarDisponibilidad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'persona_id' => 'required|exists:persona,id',
            'tiposalida_id' => 'required|exists:tiposalidas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'disponible' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $fecha = Carbon::parse($request->fecha);
        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFin = Carbon::parse($request->hora_fin);

        // Verificar si es fin de semana
        if ($fecha->isWeekend()) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'La fecha seleccionada es fin de semana'
            ]);
        }

        // Verificar si es feriado
        $feriados = Feriado::pluck('fechaf')->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))->toArray();
        if (in_array($request->fecha, $feriados)) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'La fecha seleccionada es un feriado'
            ]);
        }

        // Verificar cruce de horarios en la fecha específica
        $cruce = Salida::where('persona_id', $request->persona_id)
            ->where('tiposalida_id', $request->tiposalida_id)
            ->where('estado', '!=', 'rechazado')
            ->whereDate('fechasal', '<=', $request->fecha)
            ->whereDate('fecharet', '>=', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                // Validar cruce de horarios
                $query->where(function ($q) use ($horaInicio, $horaFin) {
                    // El nuevo horario está dentro de uno existente
                    $q->whereTime('horasal', '<=', $horaInicio)
                      ->whereTime('horaret', '>=', $horaInicio);
                })->orWhere(function ($q) use ($horaInicio, $horaFin) {
                    // El horario existente está dentro del nuevo
                    $q->whereTime('horasal', '>=', $horaInicio)
                      ->whereTime('horasal', '<=', $horaFin);
                });
            })
            ->exists();

        if ($cruce) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'El horario seleccionado ya está ocupado en esa fecha'
            ]);
        }

        // Verificar si tiene vacaciones en esa fecha
        $vacaciones = VacacionPeriodo::where('persona_id', $request->persona_id)
            ->where('estado', 'activo')
            ->whereHas('salidas', function($query) use ($request) {
                $query->where('estado', 'aprobado')
                      ->whereDate('fechasal', '<=', $request->fecha)
                      ->whereDate('fecharet', '>=', $request->fecha);
            })
            ->exists();

        if ($vacaciones) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Tiene vacaciones aprobadas en esta fecha'
            ]);
        }

        return response()->json([
            'disponible' => true,
            'mensaje' => 'Fecha y hora disponibles'
        ]);
    }

    /**
     * Obtener horarios ocupados para una fecha específica
     */
    public function horariosOcupados(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'persona_id' => 'required|exists:persona,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $horarios = Salida::where('persona_id', $request->persona_id)
            ->where('estado', '!=', 'rechazado')
            ->whereDate('fechasal', '<=', $request->fecha)
            ->whereDate('fecharet', '>=', $request->fecha)
            ->select('horasal', 'horaret', 'tiposalida_id', 'estado')
            ->with('tipoSalida')
            ->get();

        return response()->json($horarios);
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
public function registrarSalSalud(Request $request)
{
    // 1. Validar datos básicos
    $validator = Validator::make($request->all(), [
        'persona_id' => 'required|exists:persona,id',
        'fsalida' => 'required|date',
        'fretorno' => 'required|date|after_or_equal:fsalida',
        'horasal' => 'required|date_format:H:i',
        'horaret' => 'required|date_format:H:i|after:horasal',
        'fechasol' => 'required|date|before_or_equal:fsalida',
        'tipoSal' => 'required|exists:tiposalidas,id',
        'idSup' => 'required|exists:persona,id',
        'motivo' => 'nullable|string|max:255', // Agregar motivo opcional
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Buscar el servidor público
    $personal = Persona::find($request->persona_id);
    if (!$personal) {
        return response()->json(['error' => 'Persona no encontrada'], 404);
    }

    // 3. Convertir fechas
    $fsalida = Carbon::parse($request->fsalida);
    $fretorno = Carbon::parse($request->fretorno);
    $fechasol = Carbon::parse($request->fechasol);

    // 4. Validar que la fecha de solicitud no sea posterior a la salida
    if ($fechasol->gt($fsalida)) {
        return response()->json([
            'error' => 'La fecha de solicitud no puede ser posterior a la fecha de salida'
        ], 422);
    }

    // 5. OBTENER TODAS LAS SALIDAS EXISTENTES QUE SE SOLAPAN EN FECHAS
    $salidasExistentes = Salida::where('persona_id', $personal->id)
        ->where('estado', '!=', 'rechazado') // Cambiar de 'vobo' a 'estado'
        ->where(function ($query) use ($fsalida, $fretorno) {
            // Caso 1: Salida existente que abarca completamente el rango solicitado
            $query->where(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '<=', $fsalida)
                  ->where('fecharet', '>=', $fretorno);
            })
            // Caso 2: Salida existente que está dentro del rango solicitado
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '>=', $fsalida)
                  ->where('fecharet', '<=', $fretorno);
            })
            // Caso 3: Solapamiento parcial (inicio dentro)
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fechasal', '>=', $fsalida)
                  ->where('fechasal', '<=', $fretorno);
            })
            // Caso 4: Solapamiento parcial (fin dentro)
            ->orWhere(function ($q) use ($fsalida, $fretorno) {
                $q->where('fecharet', '>=', $fsalida)
                  ->where('fecharet', '<=', $fretorno);
            });
        })
        ->get();

    // 6. VERIFICAR CRUCE DE HORARIOS EN CADA FECHA DEL RANGO
    $fechaActual = clone $fsalida;
    $fechasConConflicto = [];

    while ($fechaActual <= $fretorno) {
        $fechaStr = $fechaActual->format('Y-m-d');

        // Verificar si hay alguna salida en esta fecha específica que cruce horarios
        foreach ($salidasExistentes as $salidaExistente) {
            $fechaSalida = Carbon::parse($salidaExistente->fechasal);
            $fechaRetornoSalida = Carbon::parse($salidaExistente->fecharet);

            // Verificar si la salida existente cubre esta fecha
            if ($fechaSalida <= $fechaActual && $fechaRetornoSalida >= $fechaActual) {
                // Verificar cruce de horarios en esta fecha (solo si ambas tienen hora)
                if ($salidaExistente->horasal && $salidaExistente->horaret) {
                    $horaSalidaExistente = Carbon::parse($salidaExistente->horasal);
                    $horaRetornoExistente = Carbon::parse($salidaExistente->horaret);
                    $horaSalidaNueva = Carbon::parse($request->horasal);
                    $horaRetornoNueva = Carbon::parse($request->horaret);

                    // Verificar si los horarios se superponen
                    if ($horaSalidaNueva < $horaRetornoExistente &&
                        $horaRetornoNueva > $horaSalidaExistente) {
                        $fechasConConflicto[] = $fechaStr;
                        break 2;
                    }
                }
            }
        }

        $fechaActual->addDay();
    }

    if (!empty($fechasConConflicto)) {
        return response()->json([
            'error' => "El horario seleccionado se cruza con otra salida en la(s) fecha(s): " . implode(', ', $fechasConConflicto)
        ], 422);
    }

    // 7. Verificar si ya tiene una solicitud pendiente
    $pendiente = Salida::where('persona_id', $personal->id)
        ->where('tiposalida_id', $request->tipoSal)
        ->where(function($q) {
            $q->where('estado', 'pendiente_jefe')
              ->orWhere('estado', 'pendiente_rrhh');
        })
        ->exists();

    if ($pendiente) {
        return response()->json([
            'error' => 'Ya tiene una solicitud pendiente de aprobación para este tipo de salida'
        ], 409);
    }

    // 8. Verificar que no tenga vacaciones aprobadas en el rango
    $vacacionesEnRango = Salida::where('persona_id', $personal->id)
        ->where('estado', 'aprobado')
        ->whereHas('tipoSalida', function($q) {
            $q->where('descripcion', 'LIKE', '%VACACION%');
        })
        ->where(function($query) use ($fsalida, $fretorno) {
            $query->where('fechasal', '<=', $fretorno)
                  ->where('fecharet', '>=', $fsalida);
        })
        ->exists();

    if ($vacacionesEnRango) {
        return response()->json([
            'error' => 'Tiene vacaciones aprobadas en el rango de fechas seleccionado'
        ], 422);
    }

    // 9. Calcular cantidad (días u horas según el tipo de salida)
    $tipoSalida = TipoSalida::find($request->tipoSal);
    $cantidad = null;
    if ($tipoSalida) {
        if ($tipoSalida->unidad === 'dias') {
            $cantidad = $fsalida->diffInDays($fretorno) + 1; // +1 para incluir ambos días
        } elseif ($tipoSalida->unidad === 'horas') {
            $horaSalida = Carbon::parse($request->horasal);
            $horaRetorno = Carbon::parse($request->horaret);
            $cantidad = $horaSalida->diffInHours($horaRetorno);
        }
    }

    // 10. Registrar la salida en transacción
    DB::beginTransaction();
    try {
        $salida = new Salida();
        $salida->codigo = Salida::generarCodigo();
        $salida->persona_id = $personal->id;
        $salida->tiposalida_id = $request->tipoSal;
        $salida->fechasal = $fsalida;
        $salida->horasal = $request->horasal;
        $salida->fecharet = $fretorno;
        $salida->horaret = $request->horaret;
        $salida->cantidad = $cantidad;
        $salida->motivo = $request->motivo;
        $salida->fechasol = $fechasol;

        // Campos de aprobación
        $salida->estado_jefe = 'pendiente';
        $salida->jefe_id = $request->idSup;
        $salida->estado_rrhh = 'pendiente';
        $salida->estado = 'pendiente_jefe'; // Estado consolidado inicial

        $salida->save();

        DB::commit();

        return response()->json([
            'mensaje' => 'Salida médica registrada correctamente',
            'data' => $salida
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al registrar la salida: ' . $e->getMessage()
        ], 500);
    }
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
    private function getEstadoTexto($estado)
{
    return match ($estado) {
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        default => 'Desconocido',
    };
}

private function getEstadoBadge($estado)
{
    return match ($estado) {
        'pendiente' => 'warning',
        'aprobado' => 'success',
        'rechazado' => 'danger',
        default => 'secondary',
    };
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
