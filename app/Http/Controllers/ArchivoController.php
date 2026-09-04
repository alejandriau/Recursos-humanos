<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    /**
     * Muestra el listado de archivos con filtros
     */
    public function index(Request $request)
    {
        $query = Archivo::query();
        
        // Búsqueda por título, descripción O NOMBRE DE ARCHIVO
        if ($request->has('busqueda') && $request->busqueda != '') {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('titulo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre_archivo', 'LIKE', "%{$busqueda}%");
            });
        }
        
        // Filtro por tema
        if ($request->has('tema') && $request->tema != '') {
            $query->where('tema', $request->tema);
        }
        
        // Filtro por tipo de archivo
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', $request->tipo);
        }
        
        // Ordenar por fecha (más recientes primero)
        $query->orderBy('created_at', 'desc');
        
        $archivos = $query->paginate(50);
        $temas = Archivo::distinct()->orderBy('tema')->pluck('tema');
        $tipos = Archivo::distinct()->orderBy('tipo')->pluck('tipo');
        
        return view('archivos.index', compact('archivos', 'temas', 'tipos'));
    }
    
    /**
     * Muestra el formulario para subir documentos
     */
    public function create()
    {
        $temas = Archivo::distinct()->orderBy('tema')->pluck('tema');
        return view('archivos.create', compact('temas'));
    }
    
    /**
     * Almacena un nuevo archivo
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'archivo' => 'required',
            'archivo.*' => 'file|max:307200', 
            'tema' => 'required|string|max:300',
            'descripcion' => 'nullable|string'
        ]);

        if ($request->hasFile('archivo')) {

            foreach ($request->file('archivo') as $file) {

                $nombreArchivoOriginal = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $tipo = $this->determinarTipo($extension);

                $path = $file->store('archivos', 'public');

                Archivo::create([
                    'titulo' => $request->titulo,
                    'nombre_archivo' => $nombreArchivoOriginal,
                    'ruta' => $path,
                    'extension' => $extension,
                    'tipo' => $tipo,
                    'tema' => $request->tema,
                    'descripcion' => $request->descripcion,
                    'tamano' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('documentos.index')
            ->with('success', 'Archivos subidos exitosamente.');
    }

    
    /**
     * Muestra los detalles de un archivo en modal
     */

    
    /**
     * Vista previa del archivo
     */
    public function preview($id)
    {
        $archivo = Archivo::findOrFail($id);
        
        // Verificar si el archivo existe
        if (!Storage::disk('public')->exists($archivo->ruta)) {
            abort(404, 'Archivo no encontrado');
        }
        
        // Para PDFs
        if ($archivo->extension == 'pdf') {
            return response()->file(storage_path('app/public/' . $archivo->ruta));
        }
        
        // Para imágenes
        if (in_array($archivo->extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            return response()->file(storage_path('app/public/' . $archivo->ruta));
        }
        
        // Para archivos de texto
        if (in_array($archivo->extension, ['txt', 'csv'])) {
            $contenido = Storage::disk('public')->get($archivo->ruta);
            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'inline');
        }
        
        // Para otros archivos, mostrar vista genérica
        return view('archivos.preview-generico', compact('archivo'));
    }
    
    /**
     * Descarga el archivo
     */
    public function download($id)
    {
        $archivo = Archivo::findOrFail($id);
        
        return Storage::disk('public')->download($archivo->ruta, $archivo->nombre_archivo);
    }
    
    /**
     * Muestra el formulario para editar un archivo
     */
    public function edit($id)
    {
        $archivo = Archivo::findOrFail($id);
        $temas = Archivo::distinct()->orderBy('tema')->pluck('tema');
        return view('archivos.edit', compact('archivo', 'temas'));
    }
    
    /**
     * Actualiza los metadatos de un archivo
     */
    public function update(Request $request, $id)
    {
        $archivo = Archivo::findOrFail($id);
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tema' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]);
        
        $archivo->update([
            'titulo' => $request->titulo,
            'tema' => $request->tema,
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('documentos.index')
            ->with('success', 'Archivo actualizado exitosamente.');
    }
    
    /**
     * Elimina un archivo
     */
    public function destroy($id)
    {
        $archivo = Archivo::findOrFail($id);
        
        // Eliminar el archivo físico
        Storage::disk('public')->delete($archivo->ruta);
        
        // Eliminar el registro de la base de datos
        $archivo->delete();
        
        return redirect()->route('documentos.index')
            ->with('success', 'Archivo eliminado exitosamente.');
    }
    
    /**
     * Buscar archivos en tiempo real (para autocomplete)
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        
        $archivos = Archivo::where('titulo', 'LIKE', "%{$query}%")
                          ->orWhere('descripcion', 'LIKE', "%{$query}%")
                          ->orWhere('nombre_archivo', 'LIKE', "%{$query}%")
                          ->limit(10)
                          ->get(['id', 'titulo', 'nombre_archivo', 'tipo']);
        
        return response()->json($archivos);
    }
    
    /**
     * Determina el tipo de archivo basado en la extensión
     */
    private function determinarTipo($extension)
    {
        $extension = strtolower($extension);
        
        if (in_array($extension, ['doc', 'docx', 'odt'])) {
            return 'word';
        } elseif (in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return 'excel';
        } elseif ($extension == 'pdf') {
            return 'pdf';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            return 'imagen';
        } elseif (in_array($extension, ['ppt', 'pptx'])) {
            return 'powerpoint';
        } elseif ($extension == 'txt') {
            return 'texto';
        } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
            return 'comprimido';
        } else {
            return 'otros';
        }
    }
    
    /**
     * Obtiene el icono correspondiente al tipo de archivo
     */
    private function getIcono($tipo)
    {
        $iconos = [
            'word' => 'fa-file-word text-primary',
            'excel' => 'fa-file-excel text-success',
            'pdf' => 'fa-file-pdf text-danger',
            'imagen' => 'fa-file-image text-info',
            'powerpoint' => 'fa-file-powerpoint text-warning',
            'texto' => 'fa-file-alt text-secondary',
            'comprimido' => 'fa-file-archive text-dark',
            'otros' => 'fa-file text-muted'
        ];
        
        return $iconos[$tipo] ?? $iconos['otros'];
    }
    
    /**
     * Formatea el tamaño del archivo
     */
    private function formatearTamanio($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Obtiene contenido para previsualizar en modal
     */
public function getModalData($id)
{
    try {
        $archivo = Archivo::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'archivo' => [
                'id' => $archivo->id,
                'titulo' => $archivo->titulo,
                'nombre_archivo' => $archivo->nombre_archivo,
                'descripcion' => $archivo->descripcion,
                'tipo' => $archivo->tipo,
                'tema' => $archivo->tema,
                'extension' => $archivo->extension,
                'tamano' => number_format($archivo->tamano / 1024, 2) . ' KB',
                'tamano_bytes' => $archivo->tamano,
                'fecha' => $archivo->created_at->format('d/m/Y H:i'),
                'download_url' => route('documentos.download', $archivo->id),
                'preview_url' => route('documentos.contenido-preview', $archivo->id)
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en show: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Archivo no encontrado'
        ], 404);
    }
}
public function verContenidoPreview($id)
{
    try {
        $archivo = Archivo::findOrFail($id);

        if (!Storage::disk('public')->exists($archivo->ruta)) {
            return response()->json([
                'tipo' => 'error',
                'mensaje' => 'Archivo no encontrado'
            ], 404);
        }

        $extension = strtolower($archivo->extension);
        $urlDescarga = route('documentos.ver-archivo', $archivo->id);

        // 📄 TEXTOS
        if (in_array($extension, ['txt', 'csv', 'md'])) {
            if ($archivo->tamano < 50000) {
                $contenido = Storage::disk('public')->get($archivo->ruta);
                return response()->json([
                    'tipo' => 'texto',
                    'contenido' => nl2br(e(substr($contenido, 0, 3000)))
                ]);
            }
        }

        // 🖼️ IMÁGENES
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
            return response()->json([
                'tipo' => 'imagen',
                'url' => $urlDescarga
            ]);
        }

        // 📑 PDF
        if ($extension === 'pdf') {
            return response()->json([
                'tipo' => 'pdf',
                'url' => $urlDescarga
            ]);
        }

        // 📊 EXCEL - SOLUCIÓN ESPECÍFICA
        if (in_array($extension, ['xls', 'xlsx'])) {
            return response()->json([
                'tipo' => 'excel',
                'mensaje' => 'Vista previa no disponible para Excel. Puedes descargar el archivo.',
                'url_descarga' => $urlDescarga,
                'extension' => $extension
            ]);
        }

        // 📝 WORD - SOLUCIÓN ESPECÍFICA
        if (in_array($extension, ['doc', 'docx'])) {
            return response()->json([
                'tipo' => 'word',
                'mensaje' => 'Vista previa no disponible para Word. Puedes descargar el archivo.',
                'url_descarga' => $urlDescarga,
                'extension' => $extension
            ]);
        }

        // 📽️ POWERPOINT
        if (in_array($extension, ['ppt', 'pptx'])) {
            return response()->json([
                'tipo' => 'powerpoint',
                'mensaje' => 'Vista previa no disponible para PowerPoint. Puedes descargar el archivo.',
                'url_descarga' => $urlDescarga,
                'extension' => $extension
            ]);
        }

        return response()->json([
            'tipo' => 'no_preview',
            'mensaje' => 'Vista previa no disponible para este tipo de archivo',
            'url_descarga' => $urlDescarga
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en preview: ' . $e->getMessage());
        return response()->json([
            'tipo' => 'error',
            'mensaje' => 'Error al procesar el archivo'
        ], 500);
    }
}


// En tu método verArchivo, añade CORS para Google Viewer
public function verArchivo($id)
{
    $archivo = Archivo::findOrFail($id);

    if (!Storage::disk('public')->exists($archivo->ruta)) {
        abort(404, 'Archivo no encontrado');
    }

    // Para archivos de Office, forzar descarga en lugar de visualización inline
    $extension = strtolower($archivo->extension);
    $esOffice = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    
    $contenido = Storage::disk('public')->get($archivo->ruta);
    $tipo = Storage::disk('public')->mimeType($archivo->ruta);
    
    // Si es Office, forzar descarga
    if ($esOffice) {
        return response($contenido)
            ->header('Content-Type', $tipo)
            ->header('Content-Disposition', 'attachment; filename="' . $archivo->nombre_archivo . '"');
    }

    // Para PDFs e imágenes, visualización inline
    return response($contenido)
        ->header('Content-Type', $tipo)
        ->header('Content-Disposition', 'inline; filename="' . $archivo->nombre_archivo . '"');
}

}