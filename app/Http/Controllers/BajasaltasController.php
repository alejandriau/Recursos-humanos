<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Bajasaltas;
use App\Models\Pasivodos;
use Carbon\Carbon;

class BajasaltasController extends Controller
{
    public function index(Request $request)
    {
        $query = Bajasaltas::with('persona');

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $nombre = $request->nombre;

            $query->whereHas('persona', function ($q) use ($nombre) {
                $q->where('nombre', 'like', '%' . $nombre . '%')
                ->orWhere('apellidoPat', 'like', '%' . $nombre . '%')
                ->orWhere('apellidoMat', 'like', '%' . $nombre . '%')
                ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$nombre%"]);
            });
        }


        // Filtro por fecha de baja
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }


        $bajas = $query
            ->get()
            ->map(function ($baja) {
                $fechaIngreso = $baja->persona->fechaIngreso ?? null;
                $fechaBaja = $baja->fecha;

                $tiempo = $fechaIngreso
                    ? Carbon::parse($fechaIngreso)->diff(Carbon::parse($fechaBaja))->format('%y años, %m meses, %d días')
                    : 'Sin datos';

                return [
                    'id' => $baja->id,
                    'nombre' => $baja->persona->nombre.' '.$baja->persona->apellidoPat.' '.$baja->persona->apellidoMat ,
                    'fecha_ingreso' => $fechaIngreso,
                    'fecha_baja' => $fechaBaja,
                    'motivo' => $baja->motivo,
                    'observacion' => $baja->observacion,
                    'tiempo_en_institucion' => $tiempo,
                ];
            });



        return view('admin.bajas.index', compact('bajas'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idPersona' => 'required|exists:persona,id',
            'fechafin' => 'required|date',
            'motivo' => 'required|string',
            'apellidopaterno' => 'nullable|string',
            'apellidomaterno' => 'nullable|string',
            'nombre' => 'nullable|string',
            'obser' => 'nullable|string',
            'pdffile' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        $persona = Persona::findOrFail($validated['idPersona']);
        $nombreCompleto = trim($validated['apellidopaterno'] . " " . $validated['apellidomaterno'] . " " . $validated['nombre']);
        $letra = substr($validated['apellidopaterno'] ?: $validated['apellidomaterno'], 0, 1);

        // Reutilizar o insertar nuevo en pasivodos
        $pasivo = Pasivodos::where('letra', $letra)->whereNull('nombrecompleto')->first();

        if ($pasivo) {
            $pasivo->nombrecompleto = $nombreCompleto;
            $pasivo->save();
        } else {
            $maxCodigo = Pasivodos::where('letra', $letra)->max('codigo') ?? 0;
            Pasivodos::create([
                'codigo' => $maxCodigo + 1,
                'nombrecompleto' => $nombreCompleto,
                'letra' => $letra
            ]);
        }

        // Cambiar estado
        $persona->estado = 0;
        $persona->save();

        // Guardar archivo si existe
        $pdfPath = null;
        if ($request->hasFile('pdffile')) {
            $pdfPath = $request->file('pdffile')->store('pdfs', 'public');
        }

        // Registrar baja
        Bajasaltas::create([
            'idPersona' => $validated['idPersona'],
            'fecha' => $validated['fechafin'],
            'motivo' => $validated['motivo'],
            'fecharegistro' => now(),
            'observacion' => $validated['obser'],
            'pdfbaja' => $pdfPath
        ]);

        return redirect()->route('altasbajas')->with('success', 'Baja registrada correctamente');
    }
        public function update(Request $request, $id)
    {
        $baja = Bajasaltas::findOrFail($id);

        // Validación
        $request->validate([
            'motivo' => 'required|string|max:255',
            'fecha_baja' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        // Actualización
        $baja->motivo = $request->motivo;
        $baja->fecha = $request->fecha_baja;
        $baja->observacion = $request->observaciones;
        $baja->save();

        return redirect()->route('bajasaltas.index')->with('mensaje', 'Registro de baja actualizado correctamente.');
    }
}
