<?php

namespace App\Http\Controllers;

use App\Models\Bachiller;
use App\Models\Persona;
use Illuminate\Http\Request;

class BachillerController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver bachilleres')->only(['index', 'show']);
    //    $this->middleware('permission:crear bachilleres')->only(['create', 'store']);
    //    $this->middleware('permission:editar bachilleres')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar bachilleres')->only(['destroy']);
    //}

    public function index(Request $request)
    {
        $query = Bachiller::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por observación
        if ($request->has('observacion') && $request->observacion != '') {
            $query->where('observacion', 'like', '%' . $request->observacion . '%');
        }

        // Filtro por otros
        if ($request->has('otros') && $request->otros != '') {
            $query->where('otros', 'like', '%' . $request->otros . '%');
        }

        // Filtro por fecha desde
        if ($request->has('fecha_desde') && $request->fecha_desde != '') {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $bachilleres = $query->paginate(50);

        return view('admin.bachilleres.index', compact('bachilleres'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.bachilleres.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:250',
            'otros' => 'nullable|string|max:250',
            'idPersona' => 'required|exists:persona,id'
        ]);

        Bachiller::create($request->all());

        return redirect()->route('bachilleres.index')
            ->with('success', 'Registro de bachiller creado exitosamente.');
    }

    public function show(Bachiller $bachiller)
    {
        return view('admin.bachilleres.show', compact('bachiller'));
    }

    public function edit(Bachiller $bachiller)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.bachilleres.edit', compact('bachiller', 'personas'));
    }

    public function update(Request $request, Bachiller $bachiller)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:250',
            'otros' => 'nullable|string|max:250',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $bachiller->update($request->all());

        return redirect()->route('bachilleres.index')
            ->with('success', 'Registro de bachiller actualizado exitosamente.');
    }

    public function destroy(Bachiller $bachiller)
    {
        $bachiller->delete();

        return redirect()->route('bachilleres.index')
            ->with('success', 'Registro de bachiller eliminado exitosamente.');
    }
}
