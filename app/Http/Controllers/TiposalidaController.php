<?php

namespace App\Http\Controllers;

use App\Models\Feriado;
use App\Models\Gestion;
use App\Models\Tiposalida;
use Illuminate\Http\Request;

class TiposalidaController extends Controller
{
    public function funListar()
    {
        $tipoSal = Tiposalida::orderBy('descripcion')->paginate(7);
        return view("admin.tsalida.listar", ["tipoSal" => $tipoSal]);
    }
    public function funCrear()
    {
        return view("admin.tsalida.nuevo");
    }
    public function funGuardar(Request $request)
    {
        $tiposalida = new Tiposalida;
        $desc = strtoupper($request->descripcion); // convierte descripcion de tipo de salida en mayuscula
        $tiposalida->descripcion = $desc;
        $tiposalida->sustLegal=$request->sustento;
        $tiposalida->expresa=$request->expresa;
        $tiposalida->id_padre=$request->padre;
        $tiposalida->save();
        return redirect()->back()->with("status", true);
    }
    public function funEditar($id)
    {
        $tiposalida = Tiposalida::find($id);
        $salida=Tiposalida::get();
        return view("admin.tsalida.editar", ["tiposalida" => $tiposalida,"salida"=>$salida]);
    }
    public function funModificar($id, Request $request)
    {
        $tiposalida = Tiposalida::find($id);
        $desc = strtoupper($request->descripcion);
        $tiposalida->descripcion = $desc;
        $tiposalida->sustLegal = $request->sustento;
        $tiposalida->expresa = $request->expresa;
        $tiposalida->id_padre = $request->padre;
        $tiposalida->update();
        return redirect("/tsalida")->with("upstatus", true);
    }
    public function funEliminar($id)
    {
        $tiposalida = Tiposalida::find($id);
        $tiposalida->delete();
        return redirect("/tsalida");
    }
}
