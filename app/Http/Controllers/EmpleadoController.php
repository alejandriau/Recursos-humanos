<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Historial;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    /**
     * Obtener la persona autenticada (empleado actual)
     */
    private function getEmpleadoAutenticado()
    {
        $user = Auth::user();

        // Si tu modelo User tiene relación con Persona
        if ($user->persona) {
            return $user->persona;
        }

        // Si no hay relación, buscar por CI
        return Persona::where('ci', $user->email)
                     ->where('estado', 1)
                     ->firstOrFail();
    }

    /**
     * Mostrar perfil del empleado autenticado
     */
    public function miPerfil()
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $persona = $this->getEmpleadoAutenticado();

        $persona->load([
            'profesion',
            'historial' => function ($q) {
                $q->where(function($query) {
                    $query->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', now());
                })->with([
                    'puesto.unidadOrganizacional.padre.padre.padre.padre'
                ]);
            }
        ]);

        $historial = $persona->historial->first();

        // Calcular edad y antigüedad para la vista
        $edad = $this->calcularEdad($persona->fechaNacimiento);
        $antiguedad = $this->calcularAntiguedad($persona->fechaIngreso);

        return view('empleado.perfil', compact('persona', 'historial', 'edad', 'antiguedad'));
    }

    /**
     * Mostrar historial completo del empleado autenticado
     */
    public function miHistorial()
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $persona = $this->getEmpleadoAutenticado();

        $persona->load([
            'historial' => function($q) {
                $q->with([
                    'puesto.unidadOrganizacional.padre.padre.padre.padre'
                ])->orderBy('fecha_inicio', 'desc');
            }
        ]);

        return view('empleado.historial', compact('persona'));
    }

    /**
     * Generar PDF del expediente personal
     */
    public function miExpediente()
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $persona = $this->getEmpleadoAutenticado();
        return $this->generarPdfExpediente($persona, 'download');
    }

    /**
     * Vista previa del expediente en el navegador
     */
    public function verMiExpediente()
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $persona = $this->getEmpleadoAutenticado();
        return $this->generarPdfExpediente($persona, 'stream');
    }

    /**
     * Método privado para generar PDF (reutilizable)
     */
    private function generarPdfExpediente($persona, $tipo = 'download')
    {
        $persona->load([
            'profesion',
            'historial' => function($q) {
                $q->with([
                    'puesto.unidadOrganizacional.padre.padre.padre.padre'
                ])->orderBy('fecha_inicio', 'desc');
            }
        ]);

        $historialActual = $persona->historial->whereNull('fecha_fin')->first();

        // Obtener foto en Base64 si existe
        $fotoBase64 = null;
        if ($persona->foto) {
            try {
                if (Storage::disk('public')->exists($persona->foto)) {
                    $fotoContenido = Storage::disk('public')->get($persona->foto);
                    $fotoBase64 = base64_encode($fotoContenido);
                }
            } catch (\Exception $e) {
                // Si hay error con la foto, continuar sin ella
            }
        }

        $datos = [
            'persona' => $persona,
            'historialActual' => $historialActual,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'antiguedad' => $this->calcularAntiguedad($persona->fechaIngreso),
            'edad' => $this->calcularEdad($persona->fechaNacimiento),
            'fotoBase64' => $fotoBase64
        ];

        $pdf = Pdf::loadView('empleado.expediente-pdf', $datos);
        $pdf->setPaper('A4', 'portrait');

        $nombreArchivo = "EXPEDIENTE_{$persona->ci}_{$persona->nombre}.pdf";

        if ($tipo === 'stream') {
            return $pdf->stream($nombreArchivo);
        }

        return $pdf->download($nombreArchivo);
    }

    /**
     * Métodos auxiliares
     */
    private function calcularAntiguedad($fechaIngreso)
    {
        if (!$fechaIngreso) {
            return [
                'anos' => 0,
                'meses' => 0,
                'dias' => 0,
                'total_meses' => 0
            ];
        }

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
        if (!$fechaNacimiento) {
            return 'No especificada';
        }

        return Carbon::parse($fechaNacimiento)->age;
    }
}
