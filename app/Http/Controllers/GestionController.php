<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use Illuminate\Http\Request;

class GestionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gestion1 = Gestion::get();
        $gestion = Gestion::latest('fecha')->paginate(8);
        //$gestion = Gestion::paginate(5);
        return view("admin.gestion.listar", ["gestion" => $gestion, "gestion1" => $gestion1]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("admin.gestion.nuevo");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $gestion = new Gestion;
        $gestion->anio = $request->anio;
        $gestion->fecha = $request->fecha;
        $gestion->estado = $request->estado;
        $gestion->save();
        return redirect("/apertura-gestion/gestion")->with("status", true);
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
        $gestion = Gestion::find($id);
        return view("admin.gestion.editar", ["gestion" => $gestion]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $gestion = Gestion::find($id);
        $gestion->anio = $request->anio;
        $gestion->fecha = $request->fecha;
        $gestion->estado = $request->estado;
        $gestion->update();
        return redirect("/gestion")->with("upstatus", true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $gestion = Gestion::find($id);
        $gestion->delete();
        return redirect("/gestion");
    }
}
