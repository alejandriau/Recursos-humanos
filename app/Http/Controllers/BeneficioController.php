<?php

namespace App\Http\Controllers;

use App\Models\Beneficio;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\Tiposalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficioController extends Controller
{
    public function asignarVista()
    {
        // Obtener las 3 gestiones más recientes sin importar el estado
        $gestiones = Gestion::orderBy('anio', 'desc')->take(2)->get();

        $tiposalidas = Tiposalida::all();

        return view('admin.beneficio.asigBeneficio', compact('gestiones', 'tiposalidas'));
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
        $data = $request->validate([
            'persona_id' => 'required|exists:persona,id',
            'gestion_id' => 'required|exists:gestions,id',
            'beneficios' => 'required|array',
            'beneficios.*.tiposalida_id' => 'required|exists:tiposalidas,id',
            'beneficios.*.cantidad' => 'nullable|numeric|min:0',
        ]);

        $beneficiosAsignados = [];

        foreach ($data['beneficios'] as $b) {
            $beneficio = Beneficio::updateOrCreate(
                [
                    'persona_id' => $data['persona_id'],
                    'gestion_id' => $data['gestion_id'],
                    'tiposalida_id' => $b['tiposalida_id']
                ],
                ['cantidad' => $b['cantidad']]
            );
            $beneficiosAsignados[] = $beneficio->load('tiposalida', 'persona', 'gestion');
        }

        return response()->json($beneficiosAsignados);
    }
    // Elimina un beneficio despues de asignar el beneficio al personal
    public function eliminar($id)
    {
        $beneficio = Beneficio::findOrFail($id);
        $beneficio->delete();

        return response()->json(['mensaje' => 'Beneficio eliminado correctamente']);
    }
    // Modificar el campo cantidad del beneficio
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'nullable|numeric|min:0'
        ]);

        $beneficio = Beneficio::findOrFail($id);
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

        $query = \App\Models\Beneficio::with(['gestion', 'tiposalida'])
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
