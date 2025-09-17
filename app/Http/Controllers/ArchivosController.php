<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Persona;
use App\Models\Archivos;

class ArchivosController extends Controller
{
    public function index()
    {
        return view('admin.archivos.index');
    }

    public function create()
    {
        return view('archivos.create');
    }
    public function formulario(Request $request)
    {
        $opcion = $request->input('opcion');
        $personas = Persona::find($opcion);
        return response()->view('admin.archivos.partes.formulario', compact('personas'));
    }
    public function buscar(Request $request)
    {
        $search = $request->input('search');
        $personas = Persona::where('estado', 1)
            ->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$search%"])
                    ->orWhere('ci', 'LIKE', "%$search%");
            })
            ->get();

        return response()->view('admin.archivos.partes.lista', compact('personas'));

    }
    public function v()
    {

        return response()->view('admin.pasivos.pasivosdos.partes.provar');

    }

    public function store(Request $request, $id)
    {
        // Validación del formulario
        $request->validate([
            'tipoDocumento' => 'required|string',
            'archivo' => 'required|file|max:5120', // Máx 5MB
            'observaciones' => 'nullable|string',
            'subcarpeta' => 'required|string|in:documentos_incorporacion,documentos_formacion_academica_experiencia_laboral,documentos_varios,documentos_desvinculacion'
        ]);

        // Buscar la persona por ID
        $persona = Persona::find($id);
        if (!$persona) {
            return redirect()->route('admin.personas.index')->with('error', 'Persona no encontrada');
        }

        // Verificar si la carpeta principal de la persona existe
        //$ruta = $request->file('archivo')->storeAs('persona_' . $persona->id, $nombreArchivo);
        $folderName = 'persona_' . $persona->id . '_' . Str::slug($persona->nombre . '_' . $persona->apellidoPat.'-'. $persona->apellidoMate);
        $basePath = "archivos/{$folderName}";


            // Ruta base en storage/app
    $basePath = "archivos/{$folderName}";

    // Lista de subcarpetas
    $subCarpetas = [
        'documentos_incorporacion',
        'documentos_formacion_academica_experiencia_laboral',
        'documentos_varios',
        'documentos_desvinculacion',
    ];

        // Si no existe la carpeta de la persona, crearla
    if (!Storage::disk('local')->exists($basePath)) {
        //Storage::makeDirectory($basePath);
        Storage::disk('local')->makeDirectory($basePath);

    }



        // Verificar si la subcarpeta existe dentro de la carpeta de la persona
        $subCarpeta = $request->subcarpeta;
        $subCarpetaPath = "{$basePath}/{$subCarpeta}";


        // Si la carpeta de la persona existe pero la subcarpeta no, la creamos
        if (Storage::disk('local')->exists($basePath) && !Storage::disk('local')->exists($subCarpetaPath)) {
                // Crear subcarpetas si no existen
            foreach ($subCarpetas as $sub) {
                $subPath = "{$basePath}/{$sub}";
                if (!Storage::disk('local')->exists($subCarpetaPath)) {
                    //Storage::makeDirectory($subPath);
                    Storage::disk('local')->makeDirectory($subPath);
                }
            }
        }

        // Guardar el archivo en la subcarpeta seleccionada
        $subCarpetaSelec = $request->subcarpeta;
        $nombreOriginal = $request->file('archivo')->getClientOriginalName();
        $subCarpetaPath = "{$basePath}/{$subCarpetaSelec}/{$nombreOriginal}";

        $request->file('archivo')->storeAs($subCarpetaPath, $nombreOriginal, 'local');

        // Crear el registro en la base de datos
        Archivos::create([
            'idPersona' => $id,
            'tipoDocumento' => $request->tipoDocumento,
            'rutaArchivo' => $subCarpetaPath,
            'nombreOriginal' => $nombreOriginal,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('archivos')->with('success', 'Archivo cargado correctamente en la subcarpeta seleccionada.');
    }
}
