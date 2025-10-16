<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\DjbRenta;
use App\Models\Afps;
use App\Models\Cajacordes;
use App\Models\Cenvi;
use App\Models\Bachiller;
use App\Models\Formulario1;
use App\Models\Formulario2;
use App\Models\Forconsangui;
use App\Models\CedulaIdentidad;
use App\Models\CertNacimiento;
use App\Models\LicenciaConducir;
use App\Models\LicenciaMilitar;
use App\Models\Curriculum;
use Illuminate\Http\Request;

class PersonaDashboardController extends Controller
{
    public function show($id)
    {
        $persona = Persona::with([
            'djbRenta',
            'afps',
            'cajacordes',
            'cenvis',
            'croquis',
            'cedulasIdentidad',
            'certificadosNacimiento',
            'bachilleres',
            'formularios1',
            'formularios2',
            'consanguinidades',
            'licenciasConducir',
            'licenciasMilitar',
            'curriculums',
            'profesiones',
            'certificados'
        ])->findOrFail($id);

        return view('admin.personas.dashboard', compact('persona'));
    }

    public function getResumenDocumentos($id)
    {
        $persona = Persona::findOrFail($id);

        $resumen = [
            'bachiller' => [
                'total' => $persona->bachilleres()->count(),
                'activos' => $persona->bachilleres()->where('estado', 1)->count(),
                'ultimo' => $persona->bachilleres()->latest()->first()
            ],
            'formulario1' => [
                'total' => $persona->formularios1()->count(),
                'activos' => $persona->formularios1()->where('estado', 1)->count(),
                'ultimo' => $persona->formularios1()->latest()->first()
            ],
            'formulario2' => [
                'total' => $persona->formularios2()->count(),
                'activos' => $persona->formularios2()->where('estado', 1)->count(),
                'ultimo' => $persona->formularios2()->latest()->first()
            ],
            'consanguinidad' => [
                'total' => $persona->consanguinidades()->count(),
                'activos' => $persona->consanguinidades()->where('estado', 1)->count(),
                'ultimo' => $persona->consanguinidades()->latest()->first()
            ],
            'cedula_identidad' => [
                'total' => $persona->cedulasIdentidad()->count(),
                'activos' => $persona->cedulasIdentidad()->where('estado', 1)->count(),
                'ultimo' => $persona->cedulasIdentidad()->latest()->first()
            ],
            'certificado_nacimiento' => [
                'total' => $persona->certificadosNacimiento()->count(),
                'activos' => $persona->certificadosNacimiento()->where('estado', 1)->count(),
                'ultimo' => $persona->certificadosNacimiento()->latest()->first()
            ],
            'licencia_conducir' => [
                'total' => $persona->licenciasConducir()->count(),
                'activos' => $persona->licenciasConducir()->where('estado', 1)->count(),
                'ultimo' => $persona->licenciasConducir()->latest()->first()
            ],
            'licencia_militar' => [
                'total' => $persona->licenciasMilitar()->count(),
                'activos' => $persona->licenciasMilitar()->where('estado', 1)->count(),
                'ultimo' => $persona->licenciasMilitar()->latest()->first()
            ],
            'curriculum' => [
                'total' => $persona->curriculums()->count(),
                'activos' => $persona->curriculums()->where('estado', 1)->count(),
                'ultimo' => $persona->curriculums()->latest()->first()
            ]
        ];

        return response()->json($resumen);
    }
}
