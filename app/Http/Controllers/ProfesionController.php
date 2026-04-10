<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Profesion;
use App\Models\Carrera;
use App\Models\AreaConocimiento;
use App\Models\NivelAcademico;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Exports\PersonasProfesionesExport;
use Maatwebsite\Excel\Facades\Excel;
class ProfesionController extends Controller
{
    /**
     * Listado de profesiones
     */
public function index(Request $request)
{
    // Consulta base: todas las personas
    $query = Persona::query();

    // Cargar la relación profesiones (solo activas) y sus subrelaciones
    $query->with([
        'profesiones' => function ($q) {
            $q->where('estado', 1);
        },
        'profesiones.carrera.areaConocimiento',
        'profesiones.carrera.nivelAcademico'
    ]);

    // Filtro de búsqueda (texto)
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;

        $query->where(function ($q) use ($buscar) {
            // Búsqueda en campos de la persona
            $q->where('nombre', 'like', "%$buscar%")
                ->orWhere('apellidoPat', 'like', "%$buscar%")
                ->orWhere('apellidoMat', 'like', "%$buscar%")
                ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$buscar%"]);

            // Búsqueda en campos de profesión (a través de la relación)
            $q->orWhereHas('profesiones', function ($q2) use ($buscar) {
                $q2->where('provisionN', 'like', "%$buscar%")
                    ->orWhere('diploma', 'like', "%$buscar%")
                    ->orWhereHas('carrera', function ($q3) use ($buscar) {
                        $q3->where('nombre', 'like', "%$buscar%");
                    });
            });
        });
    }

    // Filtro por área de conocimiento
    if ($request->filled('idAreaConocimiento')) {
        $query->whereHas('profesiones.carrera', function ($q) use ($request) {
            $q->where('idAreaConocimiento', $request->idAreaConocimiento);
        });
    }

    // Filtro por nivel académico
    if ($request->filled('idNivelAcademico')) {
        $query->whereHas('profesiones.carrera', function ($q) use ($request) {
            $q->where('idNivelAcademico', $request->idNivelAcademico);
        });
    }

    // Filtro por profesión principal
    if ($request->filled('esPrincipal')) {
        $query->whereHas('profesiones', function ($q) use ($request) {
            $q->where('esPrincipal', $request->esPrincipal);
        });
    }

    $personas = $query->paginate(100);

    $areas = AreaConocimiento::where('estado', true)->orderBy('nombre')->get();
    $niveles = NivelAcademico::where('estado', true)->orderBy('orden')->get();

    return view('admin.profesion.index', compact('personas', 'areas', 'niveles'));
}

    /**
     * Formulario para crear nueva profesión
     */
    public function create(Persona $persona)
    {
        $carreras = Carrera::with(['areaConocimiento', 'nivelAcademico'])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        $areas = AreaConocimiento::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $niveles = NivelAcademico::where('estado', true)
            ->orderBy('orden')
            ->get();

        // 🔥 Verificar si ya tiene una profesión principal
        $tienePrincipal = Profesion::where('idPersona', $persona->id)
            ->where('esPrincipal', true)
            ->where('estado', 1)
            ->exists();

        return view('admin.profesion.create', [
            'persona' => $persona,
            'carreras' => $carreras,
            'areas' => $areas,
            'niveles' => $niveles,
            'tienePrincipal' => $tienePrincipal
        ]);
    }

    /**
     * Guardar nueva profesión
     */
    public function store(Request $request, Persona $persona)
    {
        $data = $request->validate([
            'id_carrera' => 'required|exists:carreras,id',

            // 🔥 NUEVOS CAMPOS
            'idNivelEstudiado' => 'required|exists:niveles_academicos,id',
            'estadoEstudio' => 'required|in:en_curso,incompleto,egresado,titulado',

            'diploma' => 'nullable|string|max:200',
            'fechaTitulo' => 'nullable|date|before_or_equal:today',
            'provisionN' => 'nullable|string|max:800',
            'fechaProvision' => 'nullable|date',
            'universidad' => 'nullable|string|max:150',
            'registro' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'esPrincipal' => 'boolean',

            'pdfDiploma' => 'nullable|file|mimes:pdf|max:5120',
            'pdfProvision' => 'nullable|file|mimes:pdf|max:5120',
            'pdfcedulap' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $data['idPersona'] = $persona->id;
            $data['estado'] = 1;
            // Asegurar que esPrincipal se establezca (false si no se envía)
            $data['esPrincipal'] = $request->boolean('esPrincipal');

            // Si esta nueva profesión es la principal, quitar principal de las demás
            if ($data['esPrincipal']) {
                Profesion::where('idPersona', $persona->id)
                    ->update(['esPrincipal' => false]);
            }

            // Ruta base donde guardaremos los archivos
            $rutaBase = 'profesiones/' . $persona->id;

            // Subir y guardar el archivo Diploma
            if ($request->hasFile('pdfDiploma')) {
                $fecha = now()->format('Y-m-d');
                $nombre = 'diploma_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfDiploma')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfDiploma'] = $ruta;
            }

            // Subir y guardar el archivo Provisión
            if ($request->hasFile('pdfProvision')) {
                $fecha = now()->format('Y-m-d');
                $nombre = 'provision_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfProvision')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfProvision'] = $ruta;
            }

            // Subir y guardar el archivo Cédula Profesional
            if ($request->hasFile('pdfcedulap')) {
                $fecha = now()->format('Y-m-d');
                $nombre = 'cedula_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfcedulap')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfcedulap'] = $ruta;
            }

            // Crear el registro de profesión
            Profesion::create($data);

            DB::commit();

            return redirect()->route('profesion.index')
                ->with('success', 'Profesión registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la profesión: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Profesion $profesion)
    {
        $data = $request->validate([
            'id_carrera' => 'required|exists:carreras,id',

            // 🔥 NUEVOS
            'idNivelEstudiado' => 'required|exists:niveles_academicos,id',
            'estadoEstudio' => 'required|in:en_curso,incompleto,egresado,titulado',

            'diploma' => 'nullable|string|max:200',
            'fechaTitulo' => 'nullable|date|before_or_equal:today',
            'provisionN' => 'nullable|string|max:800',
            'fechaProvision' => 'nullable|date',
            'universidad' => 'nullable|string|max:150',
            'registro' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'esPrincipal' => 'boolean',

            'pdfDiploma' => 'nullable|file|mimes:pdf|max:5120',
            'pdfProvision' => 'nullable|file|mimes:pdf|max:5120',
            'pdfcedulap' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $persona = $profesion->persona;
            
            // Asegurar que esPrincipal se establezca (false si no se envía)
            $esPrincipal = $request->boolean('esPrincipal');
            $data['esPrincipal'] = $esPrincipal;

            // Si esta profesión es la principal, quitar principal de las demás
            if ($esPrincipal) {
                Profesion::where('idPersona', $persona->id)
                    ->where('id', '!=', $profesion->id)
                    ->update(['esPrincipal' => false]);
            }

            // Ruta base donde guardaremos los archivos
            $rutaBase = 'profesiones/' . $persona->id;

            // Subir y guardar nuevos archivos si se enviaron
            if ($request->hasFile('pdfDiploma')) {
                // Eliminar archivo anterior si existe
                if ($profesion->pdfDiploma && Storage::disk('public')->exists($profesion->pdfDiploma)) {
                    Storage::disk('public')->delete($profesion->pdfDiploma);
                }
                
                $fecha = now()->format('Y-m-d');
                $nombre = 'diploma_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfDiploma')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfDiploma'] = $ruta;
            }

            if ($request->hasFile('pdfProvision')) {
                if ($profesion->pdfProvision && Storage::disk('public')->exists($profesion->pdfProvision)) {
                    Storage::disk('public')->delete($profesion->pdfProvision);
                }
                
                $fecha = now()->format('Y-m-d');
                $nombre = 'provision_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfProvision')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfProvision'] = $ruta;
            }

            if ($request->hasFile('pdfcedulap')) {
                if ($profesion->pdfcedulap && Storage::disk('public')->exists($profesion->pdfcedulap)) {
                    Storage::disk('public')->delete($profesion->pdfcedulap);
                }
                
                $fecha = now()->format('Y-m-d');
                $nombre = 'cedula_' . $fecha . '_' . uniqid() . '.pdf';
                $ruta = $request->file('pdfcedulap')->storeAs($rutaBase, $nombre, 'public');
                $data['pdfcedulap'] = $ruta;
            }

            $profesion->update($data);

            DB::commit();

            return redirect()->route('profesion.index')
                ->with('success', 'Profesión actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la profesión: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Formulario para editar profesión
     */
    public function edit(Profesion $profesion)
    {
        // 🔥 Cargar relaciones necesarias
        $profesion->load([
            'persona',
            'carrera.areaConocimiento',
            'carrera.nivelAcademico',
            'nivelEstudiado' // 🔥 IMPORTANTE
        ]);

        $persona = $profesion->persona;

        $carreras = Carrera::with(['areaConocimiento', 'nivelAcademico'])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $areas = AreaConocimiento::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $niveles = NivelAcademico::where('estado', true)
            ->orderBy('orden')
            ->get();

        // 🔥 Verificar si ya tiene otra profesión principal
        $tieneOtraPrincipal = Profesion::where('idPersona', $persona->id)
            ->where('esPrincipal', true)
            ->where('id', '!=', $profesion->id)
            ->where('estado', 1)
            ->exists();

        return view('admin.profesion.edit', [
            'persona' => $persona,
            'profesion' => $profesion,
            'carreras' => $carreras,
            'areas' => $areas,
            'niveles' => $niveles,
            'tieneOtraPrincipal' => $tieneOtraPrincipal
        ]);
    }

    /**
     * Actualizar profesión
     */

    /**
     * Eliminar profesión (soft delete)
     */
    public function destroy(Profesion $profesion)
    {
        try {
            $profesion->estado = 0;
            $profesion->save();

            return redirect()->route('profesion.index')
                ->with('success', 'Profesión eliminada correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la profesión: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalles de una profesión
     */
    public function show(Persona $persona)
    {
        $profesion = $persona->profesiones()
            ->with([
                'carrera.areaConocimiento',
                'carrera.nivelAcademico',
                'nivelEstudiado'
            ])
            ->where('estado', 1)
            ->first(); // ✅

        return view('admin.profesion.show', compact('profesion', 'persona'));
    }
    public function export(Request $request)
    {
        try {
            return Excel::download(new PersonasProfesionesExport($request), 'profesiones_personas_' . date('Y-m-d_Hi') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    
}