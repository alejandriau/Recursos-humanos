<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilPuesto extends Model
{
    protected $table = 'perfil_puesto';

    protected $fillable = [
        'idPuesto',
        'aniosExperienciaMinimos',
        'nivelAcademicoRequerido',
        'areasConocimientoPermitidas',
        'carrerasEspecificas',
        'requiereTituloEnProvisionNacional',
        'observacion'
    ];

    protected $casts = [
        'aniosExperienciaMinimos' => 'integer',
        'areasConocimientoPermitidas' => 'array',
        'carrerasEspecificas' => 'array',
        'requiereTituloEnProvisionNacional' => 'boolean'
    ];

    /**
     * Relación con el puesto
     */
    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'idPuesto');
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