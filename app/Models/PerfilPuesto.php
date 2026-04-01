<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilPuesto extends Model
{
    protected $table = 'perfil_puesto';

    protected $fillable = [
        'id_puesto',
        'idNivelAcademico',
        'idAreaConocimiento',
        'idCarrera',
        'aniosExperienciaMinimos',
        'requiereTituloEnProvisionNacional',
        'conocimientoTexto',
        'objetivo',
        'observacion'
    ];

    public function nivelAcademico()
    {
        return $this->belongsTo(NivelAcademico::class, 'idNivelAcademico');
    }

    public function areaConocimiento()
    {
        return $this->belongsTo(AreaConocimiento::class, 'idAreaConocimiento');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'id_puesto');
    }



    /**
     * Verificar si un área de conocimiento es válida para este puesto
     */
    public function areaEsValida(string $areaNombre): bool
    {
        if (empty($this->areasConocimientoPermitidas)) {
            return true;
        }

        return in_array($areaNombre, $this->areasConocimientoPermitidas);
    }

    /**
     * Verificar si una carrera específica es válida
     */
    public function carreraEsValida(int $idCarrera): bool
    {
        if (empty($this->carrerasEspecificas)) {
            return true;
        }

        return in_array($idCarrera, $this->carrerasEspecificas);
    }

    /**
     * Obtener texto legible de los requisitos
     */
    public function getTextoRequisitosAttribute(): string
    {
        $requisitos = [];

        if ($this->nivelAcademicoRequerido) {
            $requisitos[] = "Nivel: {$this->nivelAcademicoRequerido}";
        }

        if (!empty($this->areasConocimientoPermitidas)) {
            $requisitos[] = "Áreas: " . implode(', ', $this->areasConocimientoPermitidas);
        }

        if ($this->aniosExperienciaMinimos) {
            $requisitos[] = "Experiencia: {$this->aniosExperienciaMinimos} años";
        }

        if ($this->requiereTituloEnProvisionNacional) {
            $requisitos[] = "Título en provisión nacional";
        }

        return implode(' | ', $requisitos);
    }
}