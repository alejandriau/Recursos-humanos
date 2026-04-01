<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidacionPerfil extends Model
{
    protected $table = 'validaciones_perfil';

    protected $fillable = [
        'id_puesto',
        'id_persona',
        'id_usuario',
        'resultado',
        'detalleValidacion',
        'fechaValidacion'
    ];

    protected $casts = [
        'detalleValidacion' => 'array',
        'resultado' => 'boolean',
        'fechaValidacion' => 'date'
    ];

    /**
     * Relación con el puesto
     */
    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'id_puesto');
    }

    /**
     * Relación con la persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Relación con el usuario que realizó la validación
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Scope para validaciones recientes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fechaValidacion', '>=', now()->subDays($dias));
    }

    /**
     * Scope para validaciones exitosas
     */
    public function scopeExitosas($query)
    {
        return $query->where('resultado', true);
    }
}