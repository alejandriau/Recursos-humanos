<?php

namespace App\Http\Controllers;

use App\Models\BeneficioPeriodo;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\TipoSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BeneficioController extends Controller
{
    public function asignarVista()
    {

        // Obtener tipos de salida activos que NO son padres (hojas)
        $tiposalidas = Tiposalida::where('activo', true)
            ->whereDoesntHave('hijos')
            ->get();

        // Si necesitas incluir algún tipo que sea padre pero quieras asignarlo (excepción),
        // podrías agregarlo con orWhere, pero según tu regla no.

        $gestiones = Gestion::where('estado', 'habilitado')->get();

        return view('admin.beneficio.asigBeneficio', compact('tiposalidas', 'gestiones'));

    }
    public function buscarPersonal(Request $request)
    {
        $q = $request->get('q');
        return Persona::where('nombre', 'like', "%$q%")
            ->orWhere('apellidoPat', 'like', "%$q%")
            ->orWhere('ci', 'like', "%$q%")
            ->get();
    }

    // Guardar beneficios
    public function guardarBeneficios(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'persona_id' => 'required|exists:persona,id',
        'gestion_id' => 'required|exists:gestions,id',
        'beneficios' => 'required|array|min:1',
        'beneficios.*.tiposalida_id' => 'required|exists:tiposalidas,id',
        'beneficios.*.cantidad' => 'numeric|nullable',
        'beneficios.*.unidad' => 'nullable|in:dias,horas',
    ]);

if ($validator->fails()) {
    return response()->json([
        'request' => $request->all(),
        'errors' => $validator->errors()->toArray(),
        'failed' => $validator->failed(),
    ], 422);
}

    foreach ($request->beneficios as $b) {
        $tipo = Tiposalida::find($b['tiposalida_id']);
        // Verificar que el tipo no tenga hijos (asignable)
        if ($tipo->hijos()->exists()) {
            return response()->json(['message' => 'El tipo seleccionado no es asignable.'], 422);
        }

        // Determinar unidad: si no viene en request, usar la del tipo o 'dias'
        $unidad = $b['unidad'] ?? $tipo->unidad ?? 'dias';

        // Crear el beneficio
        BeneficioPeriodo::create([
            'persona_id' => $request->persona_id,
            'tiposalida_id' => $tipo->id,
            'gestion_id' => $request->gestion_id,
            'mes' => null, // según lógica
            'unidad' => $unidad,
            'cantidad_asignada' => $b['cantidad'] ?? 0,
            'cantidad_usada' => 0,
            'cantidad_vencida' => 0,
            'arrastre' => 0,
            'saldo_disponible' => $b['cantidad'] ?? 0,
            'fecha_habilitacion' => now(),
            'estado' => 'activo',
        ]);
    }

        // Retornar los beneficios creados para refrescar tabla
        $beneficios = BeneficioPeriodo::with(['tiposalida', 'gestion', 'persona'])
            ->where('persona_id', $request->persona_id)
            ->where('gestion_id', $request->gestion_id)
            ->get();

        return response()->json($beneficios);
    }
    // Elimina un beneficio despues de asignar el beneficio al personal
    public function eliminar($id)
    {
        $beneficio = BeneficioPeriodo::findOrFail($id);
        $beneficio->delete();

        return response()->json(['mensaje' => 'Beneficio eliminado correctamente']);
    }
    // Modificar el campo cantidad del beneficio
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'nullable|numeric|min:0'
        ]);

        $beneficio = BeneficioPeriodo::findOrFail($id);
        $beneficio->cantidad = $request->cantidad;
        $beneficio->save();

        return response()->json(['mensaje' => 'Cantidad actualizada']);
    }
    // funcion para listar beneficios del persona
    public function beneficiosView()
    {
        $gestiones = \App\Models\Gestion::orderBy('anio', 'desc')->get();
        return view('usuario.reportesUsr.listarbeneficio', compact('gestiones'));
    }

    public function data(Request $request)
    {
        $idserv = $request->idserv;

        // Buscar el personal por idservidor
        $personal = \App\Models\Persona::where('idservidor', $idserv)->first();

        if (!$personal) {
            return response()->json(['data' => []]);
        }

        $query = \App\Models\BeneficioPeriodo::with(['gestion', 'tiposalida'])
            ->where('personal_id', $personal->id);

        // Filtro por gestión
        if ($request->filled('gestion')) {
            $query->whereHas('gestion', function ($q) use ($request) {
                $q->where('anio', $request->gestion);
            });
        }

        $beneficios = $query->orderByDesc('gestion_id')->get();

        // Mapear resultados
        $result = $beneficios->map(function ($b) {
            return [
                'gestion'        => $b->gestion->anio ?? '',
                'tipo'           => $b->tiposalida->descripcion ?? '',
                'dias_otorgados' => $b->cantidad,
                'expresado_en'   => $b->tiposalida->expresa ?? '',
            ];
        });

        return response()->json(['data' => $result]);
    }
}
