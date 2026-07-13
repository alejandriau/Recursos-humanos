<?php

namespace App\Http\Controllers;

use App\Models\Feriado;
use App\Models\Gestion;
use App\Models\TipoSalida;
use Illuminate\Http\Request;

class TiposalidaController extends Controller
{
    public function funListar()
    {
        $tipoSal = TipoSalida::orderBy('descripcion')->paginate(7);
        return view("admin.tsalida.listar", ["tipoSal" => $tipoSal]);
    }


    //public function funListar()
    //{
    //    $unidades      = Tiposalida::$unidades;
    //    $periodicidades = Tiposalida::$periodicidades;
 //
    //    return view('admin.tsalida.create', compact('unidades', 'periodicidades'));
    //}
    public function funCrear()
    {
        return view("admin.tsalida.nuevo");
    }
    public function funGuardar(Request $request)
    {
        $data = $request->validate([
            'descripcion'               => 'required|string|max:100|unique:tiposalidas,descripcion',
            'sustLegal'                 => 'nullable|string|max:500',
            'expresa'                   => 'nullable|string|max:50',
            'id_padre'                  => 'nullable|integer|exists:tiposalidas,id',
            'unidad'                    => 'required|in:dias,horas,mixto',
            'tiene_cupo'                => 'boolean',
            'periodicidad'              => 'required|in:ninguna,mensual,anual,evento',
            'cantidad_default'          => 'nullable|numeric|min:0.5|max:365',
            'permite_arrastre'          => 'boolean',
            'max_veces_periodo'         => 'nullable|integer|min:1',
            'usa_tabla_antiguedad'      => 'boolean',
            'requiere_aprobacion_jefe'  => 'boolean',
            'requiere_aprobacion_rrhh'  => 'boolean',
        ]);
 
        // Si no tiene cupo, limpiar campos que no aplican
        if (empty($data['tiene_cupo'])) {
            $data['periodicidad']         = 'ninguna';
            $data['cantidad_default']     = null;
            $data['permite_arrastre']     = false;
            $data['max_veces_periodo']    = null;
            $data['usa_tabla_antiguedad'] = false;
        }
 
        // Si usa tabla antigüedad, la cantidad_default no aplica
        if (!empty($data['usa_tabla_antiguedad'])) {
            $data['cantidad_default'] = null;
        }
 
        // Castear checkboxes (vienen como 'on' o null desde Blade)
        $data['tiene_cupo']               = $request->boolean('tiene_cupo');
        $data['permite_arrastre']         = $request->boolean('permite_arrastre');
        $data['usa_tabla_antiguedad']     = $request->boolean('usa_tabla_antiguedad');
        $data['requiere_aprobacion_jefe'] = $request->boolean('requiere_aprobacion_jefe');
        $data['requiere_aprobacion_rrhh'] = $request->boolean('requiere_aprobacion_rrhh');
        $data['activo']                   = true;
 
        TipoSalida::create($data);
 
        return redirect()->route('tipo-salida.salida')
            ->with('success', 'Tipo de salida registrado correctamente.');
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
