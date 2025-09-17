<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
                $q->whereNull('fecha_fin')->with([
                    'puesto.unidad',
                    'puesto.direccion',
                    'puesto.secretaria',
                    'puesto.area',
                ]);
            }
        ])->findOrFail($id);

        $historial = $persona->historial->first(); // historial actual

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
}
