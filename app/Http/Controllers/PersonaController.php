<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Puesto;
use App\Models\historial;
use App\Models\UnidadOrganizacional;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class PersonaController extends Controller
{
    // Mostrar todas las personas
    public function index()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.personas.bajasaltas', compact('personas'));
    }
    public function buscar(Request $request)
    {
        $search = $request->input('search');

        $personas = Persona::where('estado', 1)
            ->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$search%"])
                        ->orWhere('nombre', 'LIKE', "%$search%")
                        ->orWhere('apellidoPat', 'LIKE', "%$search%")
                        ->orWhere('apellidoMat', 'LIKE', "%$search%")
                        ->orWhere('ci', 'LIKE', "%$search%");
            })
            ->get();

        return view('admin.personas.partes.buscar', compact('personas'));
    }

    public function create()
    {
        return view('admin.personas.form', ['persona' => new Persona()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ci' => 'required|string|max:45|unique:persona,ci',
            'nombre' => 'required|string|max:100',
            'apellidoPat' => 'nullable|string|max:70',
            'apellidoMat' => 'nullable|string|max:70',
            'fechaIngreso' => 'nullable|date',
            'fechaNacimiento' => 'nullable|date',
            'sexo' => 'required|string|max:45',
            'telefono' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'tipo' => 'nullable|string|max:100',
            'archivo' => 'nullable|string|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $ci = $request->input('ci');
            $extension = $request->file('foto')->extension();
            $filename = $ci . '.' . $extension;

            $path = $request->file('foto')->storeAs('perfiles', $filename, 'public');
            $data['foto'] = 'perfiles/' . $filename;
        }

        // Crear la persona
        $persona = Persona::create($data);

        // Nombre carpeta personalizada
        $nombre = Str::slug($persona->nombre);
        $apellidoMat = Str::slug($persona->apellidoMat ?? 'SinApellido');
        $fecha = now()->format('Y-m-d');

        $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
        $ruta = "archivos/{$nombreCarpeta}";

        // Crear carpeta en storage/app/archivos/...
        Storage::disk('local')->makeDirectory($ruta);

        // Guardar la ruta relativa en el campo archivo
        $persona->archivo = $ruta;
        $persona->save();


        return redirect()->route('reportes.index')->with('success', 'Registro creado correctamente.');
    }


    public function edit($id)
    {
        $persona = Persona::findOrFail($id);
        return view('admin.personas.form', compact('persona'));
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $data = $request->validate([
            'ci' => 'required|string|max:45|unique:persona,ci,' . $persona->id,
            'nombre' => 'required|string|max:100',
            'apellidoPat' => 'nullable|string|max:70',
            'apellidoMat' => 'nullable|string|max:70',
            'fechaIngreso' => 'nullable|date',
            'fechaNacimiento' => 'nullable|date',
            'sexo' => 'required|string|max:45',
            'telefono' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048', // 👈 igual
            'tipo' => 'nullable|string|max:100',
            'archivo' => 'nullable|string|max:2048'
        ]);

        // Si hay una nueva foto
            if ($request->hasFile('foto')) {
                if ($persona->foto && Storage::exists($persona->foto)) {
                    Storage::delete($persona->foto);
                }

                $extension = $request->file('foto')->getClientOriginalExtension(); // Detecta .jpg, .png, etc.
                $filename = $request->input('ci') . '.' . $extension;
                $path = $request->file('foto')->storeAs('perfiles', $filename);
                $data['foto'] = $path;
            }


        $persona->update($data);

        return redirect()->route('reportes.index')->with('success', 'Registro actualizado correctamente.');
    }

public function mostrarFoto($id)
{
    $persona = Persona::findOrFail($id);

    if (Storage::exists($persona->foto)) {
        $contenido = Storage::get($persona->foto);
        $tipo = Storage::mimeType($persona->foto);
        return response($contenido)->header('Content-Type', $tipo);
    }

    abort(404, 'Archivo no encontrado');
}




    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->estado = 0;
        $persona->save();

        return redirect()->route('reportes.index')->with('success', 'Registro desactivado.');
    }
    //public function show($id)
    //{
        //$persona = Persona::with([
            //'afps',
            //'profesionn',
            //'djbrenta',
            //'bachiller',
            //'bajasaltas',
            //'cedulaidentidad',
            //'cenvi',
            //'certificados',
            //'certificadonacimiento',
            //'certifinacimiento',
            //'compromiso',
            //'croquis',
            //'curriculum',
            //'fromconsangui',
            //'formulario1',
            //'formulario2',
            //'licenciaconducir',
            //'licenciamilitar',
            //'memopuesto',
            //'otroscertific',
            //'otroscertific',
            // otras relaciones si tienes
        //])->findOrFail($id);

        //return view('admin.personas.show', compact('persona'));
    //}
public function show($id)
{
    $persona = Persona::with([
        'profesion',
        'historial' => function ($q) {
            $q->whereNull('fecha_fin')
              ->orWhere('fecha_fin', '>', now())
              ->with([
                  'puesto.unidadOrganizacional.padre.padre.padre.padre' // Cargar hasta 4 niveles de jerarquía
              ]);
        }
    ])->findOrFail($id);

    $historial = $persona->historial->first();

    return view('admin.personas.show', compact('persona', 'historial'));
}






public function actualizarRutasArchivos()
{
    $personas = Persona::whereNull('archivo')->get(); // Solo las que no tienen la ruta

    foreach ($personas as $persona) {
        // Preparar nombre de carpeta
        $nombre = Str::slug($persona->nombre);
        $apellidoMat = Str::slug($persona->apellidoMat ?? 'SinApellido');
        $fecha = now()->format('Y-m-d');

        $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
        $ruta = "archivos/{$nombreCarpeta}";

        // Crear la carpeta
        Storage::disk('local')->makeDirectory($ruta);

        // Guardar la ruta en la base de datos
        $persona->archivo = $ruta;
        $persona->save();
    }

    return "Rutas de archivo actualizadas correctamente para " . count($personas) . " personas.";
}





public function historial($id)
    {
        $persona = Persona::with([
            'historialPuestos.puesto.unidadOrganizacional',
            'puestoActual.puesto.unidadOrganizacional'
        ])->findOrFail($id);

        $puestos = Puesto::where('estado', 1)->get();
        $unidades = UnidadOrganizacional::where('estado', 1)->get();

        return view('admin.personas.historial', compact('persona', 'puestos', 'unidades'));
    }

    /**
     * Almacenar un nuevo registro en el historial
     */
    public function storeHistorial(Request $request, $id)
    {
        $request->validate([
            'puesto_id' => 'required|exists:puestos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'observaciones' => 'nullable|string|max:500'
        ]);

        Historial::create([
            'persona_id' => $id,
            'puesto_id' => $request->puesto_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'observaciones' => $request->observaciones,
            'usuario_registro' => auth()->id()
        ]);

        return redirect()->route('personas.historial', $id)
            ->with('success', 'Registro de historial agregado correctamente.');
    }

    /**
     * Actualizar un registro del historial
     */
    public function updateHistorial(Request $request, $id, $historialId)
    {
        $request->validate([
            'puesto_id' => 'required|exists:puestos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $historial = Historial::where('id', $historialId)
            ->where('persona_id', $id)
            ->firstOrFail();

        $historial->update([
            'puesto_id' => $request->puesto_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'observaciones' => $request->observaciones,
            'usuario_actualizacion' => auth()->id()
        ]);

        return redirect()->route('personas.historial', $id)
            ->with('success', 'Registro de historial actualizado correctamente.');
    }

    /**
     * Eliminar un registro del historial
     */
    public function destroyHistorial($id, $historialId)
    {
        $historial = Historial::where('id', $historialId)
            ->where('persona_id', $id)
            ->firstOrFail();

        $historial->delete();

        return redirect()->route('personas.historial', $id)
            ->with('success', 'Registro de historial eliminado correctamente.');
    }


        public function generarExpediente($id)
    {
        $persona = Persona::with([
            'profesion',
            'historial' => function($q) {
                $q->with([
                    'puesto.unidadOrganizacional.padre.padre.padre.padre'
                ])->orderBy('fecha_inicio', 'desc');
            },
            'historial.puesto' => function($q) {
                $q->with(['unidadOrganizacional']);
            }
        ])->findOrFail($id);

        $historialActual = $persona->historial->whereNull('fecha_fin')->first();

        $datos = [
            'persona' => $persona,
            'historialActual' => $historialActual,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'antiguedad' => $this->calcularAntiguedad($persona->fechaIngreso),
            'edad' => $this->calcularEdad($persona->fechaNacimiento)
        ];

        $pdf = Pdf::loadView('admin.personas.expediente-pdf', $datos);

        // Configurar el papel y orientación
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-right', 15);
        $pdf->setOption('margin-bottom', 15);
        $pdf->setOption('margin-left', 15);

        $nombreArchivo = "EXPEDIENTE_{$persona->ci}_{$persona->apellidoPat}_{$persona->nombre}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Vista previa del expediente en el navegador
     */
    public function verExpediente($id)
    {
        $persona = Persona::with([
            'profesion',
            'historial' => function($q) {
                $q->with([
                    'puesto.unidadOrganizacional.padre.padre.padre.padre'
                ])->orderBy('fecha_inicio', 'desc');
            },
            'historial.puesto' => function($q) {
                $q->with(['unidadOrganizacional']);
            }
        ])->findOrFail($id);

        $historialActual = $persona->historial->whereNull('fecha_fin')->first();

        $datos = [
            'persona' => $persona,
            'historialActual' => $historialActual,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'antiguedad' => $this->calcularAntiguedad($persona->fechaIngreso),
            'edad' => $this->calcularEdad($persona->fechaNacimiento)
        ];

        $pdf = Pdf::loadView('admin.personas.expediente-pdf', $datos);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("EXPEDIENTE_{$persona->ci}.pdf");
    }

    private function calcularAntiguedad($fechaIngreso)
    {
        $ingreso = Carbon::parse($fechaIngreso);
        $hoy = Carbon::now();

        $diferencia = $ingreso->diff($hoy);

        return [
            'anos' => $diferencia->y,
            'meses' => $diferencia->m,
            'dias' => $diferencia->d,
            'total_meses' => ($diferencia->y * 12) + $diferencia->m
        ];
    }

    private function calcularEdad($fechaNacimiento)
    {
        return Carbon::parse($fechaNacimiento)->age;
    }
}
