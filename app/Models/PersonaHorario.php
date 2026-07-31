<?php

// ==================== app/Models/PersonaHorario.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaHorario extends Model
{
    use HasFactory;
    protected $table = 'persona_horarios';
    protected $fillable = ['persona_id', 'horario_id', 'fecha_inicio', 'fecha_fin', 'activo'];
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }
    
}