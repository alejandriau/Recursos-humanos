<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Profesion extends Model
{
    protected $table = 'profesion';

    // Desactivar timestamps automáticos porque la tabla tiene campos personalizados
    public $timestamps = false;

    protected $fillable = [
        'idPersona',
        'id_carrera',
        'idNivelEstudiado',
        'estadoEstudio',
        'diploma',
        'fechaTitulo',         // CAMBIADO: antes era fechaDiploma
        'provisionN',
        'fechaProvision',
        'universidad',
        'registro',
        'pdfDiploma',
        'pdfProvision',
        'cedulaProfesion',
        'pdfcedulap',
        'observacion',
        'esPrincipal',         // NUEVO: Para marcar profesión principal
        'estado'
    ];

    protected $casts = [
        'fechaTitulo' => 'date',
        'fechaProvision' => 'date',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
        'esPrincipal' => 'boolean',
        'estado' => 'integer'
    ];

    /**
     * Relación con la persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    /**
     * Relación con la carrera (NUEVO)
     */
    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    /**
     * Scope para obtener solo la profesión principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('esPrincipal', true);
    }

    /**
     * Scope para profesiones activas
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Calcular años de experiencia desde la titulación
     */
    public function getAniosExperienciaDesdeTitulacionAttribute(): float
    {
        if (!$this->fechaTitulo) {
            return 0;
        }

        return Carbon::parse($this->fechaTitulo)->diffInYears(Carbon::now());
    }

    /**
     * Calcular meses de experiencia desde la titulación
     */
    public function getMesesExperienciaDesdeTitulacionAttribute(): int
    {
        if (!$this->fechaTitulo) {
            return 0;
        }

        return Carbon::parse($this->fechaTitulo)->diffInMonths(Carbon::now());
    }

    /**
     * Verificar si tiene título en provisión nacional
     */
    public function getTieneTituloProvisionAttribute(): bool
    {
        return !empty($this->provisionN) && !empty($this->fechaProvision);
    }

    /**
     * Verificar si el título es válido (tiene fecha de titulación)
     */
    public function getTituloValidoAttribute(): bool
    {
        return !is_null($this->fechaTitulo);
    }

    /**
     * Obtener el nombre completo del título
     */
    public function getNombreCompletoTituloAttribute(): string
    {
        $carreraNombre = $this->carrera ? $this->carrera->nombre : 'Sin carrera';
        $nivelNombre = $this->carrera && $this->carrera->nivelAcademico ? $this->carrera->nivelAcademico->nombre : '';
        $universidad = $this->universidad ? " - {$this->universidad}" : '';
        
        return "{$nivelNombre} en {$carreraNombre}{$universidad}";
    }

    /**
     * Obtener área de conocimiento
     */
    public function getAreaConocimientoAttribute()
    {
        return $this->carrera ? $this->carrera->areaConocimiento : null;
    }

    /**
     * Obtener nivel académico
     */
    public function getNivelAcademicoAttribute()
    {
        return $this->carrera ? $this->carrera->nivelAcademico : null;
    }

    public function nivelEstudiado()
    {
        return $this->belongsTo(NivelAcademico::class, 'idNivelEstudiado');
    }

}
