<?php

namespace App\Http\Controllers;

use App\Models\TipoSalida;
use Illuminate\Http\Request;

class TipoSalidaController extends Controller
{
    /**
     * Listado con paginación.
     */
    public function index()
    {
        $tipoSal = TipoSalida::orderBy('descripcion')->paginate(20);
        return view('admin.tsalida.index', compact('tipoSal'));
    }

    /**
     * Almacenar un nuevo tipo de salida.
     */
    public function store(Request $request)
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

        return redirect()->route('gestion.index')
            ->with('status', 'Tipo de salida registrado correctamente.');
    }

    /**
     * Mostrar formulario de edición (devuelve los datos en JSON para el modal).
     */
    public function edit($id)
    {
        $tiposalida = TipoSalida::findOrFail($id);
        return response()->json($tiposalida);
    }

    /**
     * Actualizar un tipo de salida.
     */
    public function update(Request $request, $id)
    {
        $tiposalida = TipoSalida::findOrFail($id);

        $data = $request->validate([
            'descripcion'               => 'required|string|max:100|unique:tiposalidas,descripcion,' . $id,
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
            'activo'                    => 'boolean',
        ]);



        $data['tiene_cupo']               = $request->boolean('tiene_cupo');
        $data['permite_arrastre']         = $request->boolean('permite_arrastre');
        $data['usa_tabla_antiguedad']     = $request->boolean('usa_tabla_antiguedad');
        $data['requiere_aprobacion_jefe'] = $request->boolean('requiere_aprobacion_jefe');
        $data['requiere_aprobacion_rrhh'] = $request->boolean('requiere_aprobacion_rrhh');
        $data['activo']                   = $request->boolean('activo');

        $tiposalida->update($data);

        return redirect()->route('gestion.index')
            ->with('upstatus', 'Tipo de salida actualizado correctamente.');
    }

    /**
     * Eliminar un tipo de salida.
     */
    public function destroy($id)
    {
        $tiposalida = TipoSalida::findOrFail($id);
        $tiposalida->delete();

        return redirect()->route('gestion.index')
            ->with('delstatus', 'Tipo de salida eliminado.');
    }
}