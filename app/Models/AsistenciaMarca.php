<?php
// ==================== app/Models/AsistenciaMarca.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaMarca extends Model
{
    use HasFactory;
    protected $table = 'asistencia_marcas';
    protected $fillable = [
        'asistencia_diaria_id', 'tipo_marca', 'hora_esperada', 'hora_real',
        'marcacion_id', 'estado', 'diferencia_minutos', 'salida_id',
    ];
        protected $casts = [
        'hora_esperada' => 'datetime:H:i:s',
        'hora_real' => 'datetime:H:i:s',
        'diferencia_minutos' => 'integer',
    ];

    public function asistenciaDiaria(): BelongsTo
    {
        return $this->belongsTo(AsistenciaDiaria::class, 'asistencia_diaria_id');
    }

    public function marcacion(): BelongsTo
    {
        return $this->belongsTo(MarcacionBiometrica::class, 'marcacion_id');
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

}

