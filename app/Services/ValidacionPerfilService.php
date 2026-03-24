<?php

namespace App\Services;

use App\Models\Puesto;
use App\Models\Persona;
use App\Models\Profesion;
use App\Models\PerfilPuesto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ValidacionPerfilService
{
    /**
     * Validar si una persona cumple con el perfil del puesto
     */
    public function validar(Puesto $puesto, Persona $persona): array
    {
        $perfilPuesto = PerfilPuesto::where('id_usuario', $puesto->id)->first();

        if (!$perfilPuesto) {
            return [
                'cumple' => false,
                'mensaje' => 'El puesto no tiene perfil definido',
                'detalle' => null
            ];
        }

        // Obtener la profesión principal de la persona
        $profesionPrincipal = Profesion::where('idPersona', $persona->id)
            ->where('esPrincipal', true)
            ->where('estado', 1)
            ->with(['carrera.areaConocimiento', 'carrera.nivelAcademico'])
            ->first();

        if (!$profesionPrincipal) {
            return [
                'cumple' => false,
                'mensaje' => 'La persona no tiene una profesión principal registrada',
                'detalle' => null
            ];
        }

        $resultados = [
            'cumpleFormacion' => false,
            'cumpleExperiencia' => false,
            'cumpleTituloProvision' => false,
            'detalleFormacion' => '',
            'detalleExperiencia' => '',
            'detalleTituloProvision' => '',
            'fechaTitulacion' => null,
            'aniosExperiencia' => 0,
        ];

        // 1. Validar formación académica (nivel y área)
        $resultados = $this->validarFormacion($profesionPrincipal, $perfilPuesto, $resultados);

        // 2. Validar título en provisión nacional
        $resultados = $this->validarTituloProvision($profesionPrincipal, $perfilPuesto, $resultados);

        // 3. Validar años de experiencia (desde la fecha de titulación)
        if ($profesionPrincipal->fechaTitulo && $perfilPuesto->aniosExperienciaMinimos) {
            $resultados = $this->validarExperiencia($profesionPrincipal, $perfilPuesto, $resultados);
        } else {
            $resultados['detalleExperiencia'] = $profesionPrincipal->fechaTitulo 
                ? 'No se requiere experiencia mínima' 
                : 'No tiene fecha de titulación registrada';
        }

        // Determinar si cumple globalmente
        $cumpleGlobal = $resultados['cumpleFormacion'] && 
                        $resultados['cumpleExperiencia'] && 
                        $resultados['cumpleTituloProvision'];

        return [
            'cumple' => $cumpleGlobal,
            'mensaje' => $cumpleGlobal ? 'CUMPLE con el perfil del puesto' : 'NO CUMPLE con el perfil del puesto',
            'detalle' => $resultados
        ];
    }

    /**
     * Validar formación: nivel académico y área de conocimiento
     */
    private function validarFormacion($profesion, $perfilPuesto, $resultados)
    {
        $carrera = $profesion->carrera;
        $nivelAcademico = $carrera->nivelAcademico;
        $areaConocimiento = $carrera->areaConocimiento;

        // Validar nivel académico requerido
        $nivelRequerido = $perfilPuesto->nivelAcademicoRequerido;
        $nivelesValidos = ['Licenciatura', 'Ingeniería']; // Puedes expandir

        $cumpleNivel = in_array($nivelRequerido, $nivelesValidos) && 
                       in_array($nivelAcademico->nombre, $nivelesValidos);

        // Validar área de conocimiento
        $areasPermitidas = $perfilPuesto->areasConocimientoPermitidas ?? [];
        $cumpleArea = empty($areasPermitidas) || 
                      in_array($areaConocimiento->nombre, $areasPermitidas);

        $resultados['cumpleFormacion'] = $cumpleNivel && $cumpleArea;
        $resultados['detalleFormacion'] = sprintf(
            'Nivel: %s (%s) - Área: %s (%s)',
            $nivelAcademico->nombre,
            $cumpleNivel ? '✓' : '✗',
            $areaConocimiento->nombre,
            $cumpleArea ? '✓' : '✗'
        );

        return $resultados;
    }

    /**
     * Validar título en provisión nacional
     */
    private function validarTituloProvision($profesion, $perfilPuesto, $resultados)
    {
        $requiereProvision = $perfilPuesto->requiereTituloEnProvisionNacional;
        
        if (!$requiereProvision) {
            $resultados['cumpleTituloProvision'] = true;
            $resultados['detalleTituloProvision'] = 'No se requiere provisión nacional';
            return $resultados;
        }

        $tieneProvision = !empty($profesion->provisionN) && !empty($profesion->fechaProvision);
        
        $resultados['cumpleTituloProvision'] = $tieneProvision;
        $resultados['detalleTituloProvision'] = $tieneProvision 
            ? "Tiene provisión nacional: {$profesion->provisionN}" 
            : "No tiene provisión nacional registrada";

        return $resultados;
    }

    /**
     * Validar experiencia: años desde la fecha de titulación
     */
    private function validarExperiencia($profesion, $perfilPuesto, $resultados)
    {
        $fechaTitulacion = Carbon::parse($profesion->fechaTitulo);
        $fechaActual = Carbon::now();
        
        // Calcular años desde la titulación
        $aniosDesdeTitulacion = $fechaTitulacion->diffInYears($fechaActual);
        $mesesDesdeTitulacion = $fechaTitulacion->diffInMonths($fechaActual);
        
        $aniosRequeridos = $perfilPuesto->aniosExperienciaMinimos;
        $cumpleExperiencia = $aniosDesdeTitulacion >= $aniosRequeridos;
        
        $resultados['cumpleExperiencia'] = $cumpleExperiencia;
        $resultados['aniosExperiencia'] = $aniosDesdeTitulacion;
        $resultados['fechaTitulacion'] = $fechaTitulacion->format('d/m/Y');
        $resultados['detalleExperiencia'] = sprintf(
            'Titulación: %s | Experiencia: %.1f años (%.0f meses) | Requerido: %d años | %s',
            $fechaTitulacion->format('d/m/Y'),
            $aniosDesdeTitulacion,
            $mesesDesdeTitulacion,
            $aniosRequeridos,
            $cumpleExperiencia ? '✓ CUMPLE' : '✗ NO CUMPLE'
        );
        
        return $resultados;
    }

    /**
     * Obtener resumen del perfil del puesto
     */
    public function getResumenPerfilPuesto(Puesto $puesto): array
    {
        $perfil = PerfilPuesto::where('id_usuario', $puesto->id)->first();
        
        if (!$perfil) {
            return [
                'puesto' => $puesto->denominacion,
                'perfilDefinido' => false
            ];
        }

        return [
            'puesto' => $puesto->denominacion,
            'perfilDefinido' => true,
            'requisitos' => [
                'nivel_academico' => $perfil->nivelAcademicoRequerido ?? 'No especificado',
                'areas_conocimiento' => $perfil->areasConocimientoPermitidas ?? [],
                'anios_experiencia_minimos' => $perfil->aniosExperienciaMinimos ?? 0,
                'requiere_titulo_provision' => $perfil->requiereTituloEnProvisionNacional
            ]
        ];
    }
}