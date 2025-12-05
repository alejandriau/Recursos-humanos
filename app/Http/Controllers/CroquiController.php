<?php

namespace App\Http\Controllers;

use App\Models\Croqui;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CroquiController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver croquis')->only(['index', 'show', 'mapa']);
    //    $this->middleware('permission:crear croquis')->only(['create', 'store']);
    //    $this->middleware('permission:editar croquis')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar croquis')->only(['destroy']);
    //    $this->middleware('permission:ver mapa croquis')->only(['mapa']);
    //}
    /**
 * Geocodificar dirección
 */
    public function geocode(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500'
        ]);

        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $address = urlencode($request->address);

        try {
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $address,
                'key' => $apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                $formatted_address = $data['results'][0]['formatted_address'];

                return response()->json([
                    'success' => true,
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                    'formatted_address' => $formatted_address
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró la dirección'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la geocodificación'
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = Croqui::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por dirección
        if ($request->has('direccion') && $request->direccion != '') {
            $query->where('direccion', 'like', '%' . $request->direccion . '%');
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $croquis = $query->paginate(50);

        return view('admin.croquis.index', compact('croquis'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.croquis.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'longetud' => 'required|numeric|between:-180,180',
            'latitud' => 'required|numeric|between:-90,90',
            'idPersona' => 'required|exists:persona,id'
        ]);

        Croqui::create($request->all());

        return redirect()->route('croquis.index')
            ->with('success', 'Croquis creado exitosamente.');
    }

    public function show(Croqui $croqui)
    {
        return view('admin.croquis.show', compact('croqui'));
    }

    public function edit(Croqui $croqui)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.croquis.edit', compact('croqui', 'personas'));
    }

    public function update(Request $request, Croqui $croqui)
    {
        $request->validate([
            'direccion' => 'required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'longetud' => 'required|numeric|between:-180,180',
            'latitud' => 'required|numeric|between:-90,90',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $croqui->update($request->all());

        return redirect()->route('croquis.index')
            ->with('success', 'Croquis actualizado exitosamente.');
    }

    public function destroy(Croqui $croqui)
    {
        $croqui->delete();

        return redirect()->route('croquis.index')
            ->with('success', 'Croquis eliminado exitosamente.');
    }

    public function mapa()
    {
        $croquis = Croqui::with('persona')->where('estado', 1)->get();
        return view('admin.croquis.mapa', compact('croquis'));
    }

    public function getCroquisData()
    {
        $croquis = Croqui::with('persona')->where('estado', 1)->get()->map(function($croqui) {
            return [
                'id' => $croqui->id,
                'direccion' => $croqui->direccion,
                'descripcion' => $croqui->descripcion,
                'persona' => $croqui->persona->nombre ?? 'N/A',
                'lat' => (float) $croqui->latitud,
                'lng' => (float) $croqui->longitud,
                'google_maps_link' => $croqui->google_maps_link,
                'edit_url' => route('croquis.edit', $croqui),
                'show_url' => route('croquis.show', $croqui)
            ];
        });

        return response()->json($croquis);
    }
}
