<?php

namespace App\Http\Controllers;

use App\Models\Feriado;
use App\Models\Gestion;
use Illuminate\Http\Request;

class FeriadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // funcion mostrar feriados en homeuser
    // revisar esta funcion funListFer() que muestra feriados en homeUsr
    public function funListFer()
    {
        $idg=0;
        $gestiones = Gestion::where('estado', 'Habilitado')->get();
        foreach ($gestiones as $ges) {
                $idg = $ges->id;
        }
        $feriado = Feriado::where('gestion_id', $idg)->orderBy('fechaf', 'ASC')->paginate(14);
        // $gestion = Gestion::latest('fecha')->paginate(10);
        return view("homeusr", ["feriado" => $feriado, "gestiones" => $gestiones]);
    }
    //
    public function index()
    {
        //
        $idg=0;
        $gestiones = Gestion::where('estado', 'Habilitado')->get();
        foreach ($gestiones as $ges) {
                $idg = $ges->id;
        }
        $feriado = Feriado::where('gestion_id', $idg)->orderBy('fechaf', 'ASC')->paginate(8);
        // $gestion = Gestion::latest('fecha')->paginate(10);
        return view("admin.feriado.listar", ["feriado" => $feriado, "gestiones" => $gestiones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        // $gestiones= Gestion::get();
        //return view("admin.gestion.nuevo",compact('gestiones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //
        $desc=($request->descripcion);
        $feriado = new Feriado;
        $feriado->fechaf = $request->fecha;
        $feriado->descripcion = $desc;
        $feriado->gestion_id = $request->gestion;
        $feriado->save();
        return redirect()->back()->with("status", true);
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
        $feriado = Feriado::find($id);
        return view("admin.feriado.editar", ["feriado" => $feriado]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $feriado = Feriado::find($id);
        $feriado->descripcion = $request->descripcion;
        $feriado->fechaf = $request->fecha;
        $feriado->update();
        return redirect("/feriado-gestion/feriado")->with("upstatus", true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $feriado = Feriado::find($id);
        $feriado->delete();
        return redirect("/feriado-gestion/feriado");
    }
}
