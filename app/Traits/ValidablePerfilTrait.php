<?php

namespace App\Traits;

use App\Models\PerfilPuesto;
use App\Models\Profesion;
use Carbon\Carbon;

trait ValidablePerfilTrait
{
    /**
     * Validar si una persona cumple con el perfil del puesto
     */
    public function validarPerfilPuesto($puesto, $persona): array
    {
        $perfil = PerfilPuesto::where('id_puesto', $puesto->id)->first();

        if (!$perfil) {
            return [
                'cumple' => false,
                'mensaje' => 'El puesto no tiene perfil definido',
                'detalle' => null
            ];
        }

        $profesion = $persona->profesionPrincipal;

        if (!$profesion) {
            return [
                'cumple' => false,
                'mensaje' => 'La persona no tiene profesión principal registrada',
                'detalle' => null
            ];
        }

        $validaciones = [
            'formacion' => $this->validarFormacion($profesion, $perfil),
            'experiencia' => $this->validarExperiencia($profesion, $perfil),
            'titulo_provision' => $this->validarTituloProvision($profesion, $perfil)
        ];

        $cumple = $validaciones['formacion']['cumple'] && 
                  $validaciones['experiencia']['cumple'] && 
                  $validaciones['titulo_provision']['cumple'];

        return [
            'cumple' => $cumple,
            'mensaje' => $cumple ? 'CUMPLE con el perfil del puesto' : 'NO CUMPLE con el perfil del puesto',
            'detalle' => $validaciones
        ];
    }

    /**
     * Validar formación académica
     */
    protected function validarFormacion($profesion, $perfil): array
    {
        $carrera = $profesion->carrera;
        
        if (!$carrera) {
            return ['cumple' => false, 'detalle' => 'No tiene carrera asociada'];
        }

        $nivelCumple = $this->validarNivelAcademico($carrera, $perfil);
        $areaCumple = $this->validarAreaConocimiento($carrera, $perfil);
        $carreraEspecificaCumple = $this->validarCarreraEspecifica($carrera, $perfil);

        $cumple = $nivelCumple && $areaCumple && $carreraEspecificaCumple;

        return [
            'cumple' => $cumple,
            'detalle' => [
                'nivel' => $nivelCumple,
                'area' => $areaCumple,
                'carrera_especifica' => $carreraEspecificaCumple,
                'carrera' => $carrera->nombre,
                'nivel_academico' => $carrera->nivelAcademico->nombre ?? 'No definido',
                'area_conocimiento' => $carrera->areaConocimiento->nombre ?? 'No definido'
            ]
        ];
    }

    /**
     * Validar nivel académico requerido
     */
    protected function validarNivelAcademico($carrera, $perfil): bool
    {
        if (!$perfil->nivelAcademicoRequerido) {
            return true;
        }

        $nivelCarrera = $carrera->nivelAcademico->nombre ?? '';
        
        return strcasecmp($nivelCarrera, $perfil->nivelAcademicoRequerido) === 0;
    }

    /**
     * Validar área de conocimiento
     */
    protected function validarAreaConocimiento($carrera, $perfil): bool
    {
        $areasPermitidas = $perfil->areasConocimientoPermitidas ?? [];
        
        if (empty($areasPermitidas)) {
            return true;
        }

        $areaCarrera = $carrera->areaConocimiento->nombre ?? '';
        
        return in_array($areaCarrera, $areasPermitidas);
    }

    /**
     * Validar carrera específica
     */
    protected function validarCarreraEspecifica($carrera, $perfil): bool
    {
        $carrerasEspecificas = $perfil->carrerasEspecificas ?? [];
        
        if (empty($carrerasEspecificas)) {
            return true;
        }

        return in_array($carrera->id, $carrerasEspecificas);
    }

    /**
     * Validar experiencia desde titulación
     */
    protected function validarExperiencia($profesion, $perfil): array
    {
        $aniosRequeridos = $perfil->aniosExperienciaMinimos ?? 0;
        
        if ($aniosRequeridos == 0) {
            return [
                'cumple' => true,
                'detalle' => 'No se requiere experiencia mínima'
            ];
        }

        if (!$profesion->fechaTitulo) {
            return [
                'cumple' => false,
                'detalle' => 'No tiene fecha de titulación registrada'
            ];
        }

        $aniosExperiencia = Carbon::parse($profesion->fechaTitulo)->diffInYears(Carbon::now());
        $mesesExperiencia = Carbon::parse($profesion->fechaTitulo)->diffInMonths(Carbon::now());
        $cumple = $aniosExperiencia >= $aniosRequeridos;

        return [
            'cumple' => $cumple,
            'detalle' => [
                'fecha_titulacion' => $profesion->fechaTitulo->format('d/m/Y'),
                'anios_experiencia' => round($aniosExperiencia, 1),
                'meses_experiencia' => $mesesExperiencia,
                'anios_requeridos' => $aniosRequeridos,
                'cumple_requisito' => $cumple
            ]
        ];
    }

    /**
     * Validar título en provisión nacional
     */
    protected function validarTituloProvision($profesion, $perfil): array
    {
        if (!$perfil->requiereTituloEnProvisionNacional) {
            return [
                'cumple' => true,
                'detalle' => 'No se requiere título en provisión nacional'
            ];
        }

        $tieneProvision = !empty($profesion->provisionN) && !empty($profesion->fechaProvision);

        return [
            'cumple' => $tieneProvision,
            'detalle' => $tieneProvision 
                ? "Tiene provisión nacional: {$profesion->provisionN}"
                : 'No tiene título en provisión nacional registrado'
        ];
    }
}