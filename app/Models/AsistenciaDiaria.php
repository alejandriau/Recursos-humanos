<?php
// ==================== app/Models/AsistenciaDiaria.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsistenciaDiaria extends Model
{
    use HasFactory;
    protected $table = 'asistencia_diaria';
    protected $fillable = [
        'persona_id', 'fecha', 'horario_id', 'estado', 'minutos_tardanza',
        'total_marcas_esperadas', 'total_marcas_cumplidas',
        'total_marcas_justificadas', 'total_marcas_injustificadas', 'procesado_en',
    ];
    protected $casts = [
        'fecha' => 'date',
        'procesado_en' => 'datetime',
        'minutos_tardanza' => 'integer',
        'total_marcas_esperadas' => 'integer',
        'total_marcas_cumplidas' => 'integer',
        'total_marcas_justificadas' => 'integer',
        'total_marcas_injustificadas' => 'integer',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function marcas(): HasMany
    {
        return $this->hasMany(AsistenciaMarca::class, 'asistencia_diaria_id');
    }

    public function estadoLabel(): string
    {
        return match($this->estado) {
            'completo' => 'Completo',
            'tardanza' => 'Con tardanza',
            'falta_justificada' => 'Falta justificada',
            'falta_injustificada' => 'Falta injustificada',
            'incompleto' => 'Incompleto',
            'no_laborable' => 'No laborable',
            default => $this->estado,
        };
    }
    // app/Models/HorarioDia.php
    public function checkpoints(): array
    {
        $checkpoints = [];

        if ($this->entrada_manana) {
            $checkpoints['entrada_manana'] = $this->entrada_manana->format('H:i:s');
        }
        if ($this->salida_manana) {
            $checkpoints['salida_manana'] = $this->salida_manana->format('H:i:s');
        }
        if ($this->entrada_tarde) {
            $checkpoints['entrada_tarde'] = $this->entrada_tarde->format('H:i:s');
        }
        if ($this->salida_tarde) {
            $checkpoints['salida_tarde'] = $this->salida_tarde->format('H:i:s');
        }

        // Si es jornada simple (solo entrada y salida sin especificar mañana/tarde)
        if (empty($checkpoints) && $this->hora_entrada && $this->hora_salida) {
            $checkpoints['entrada'] = $this->hora_entrada->format('H:i:s');
            $checkpoints['salida']  = $this->hora_salida->format('H:i:s');
        }

        return $checkpoints;
    }
}