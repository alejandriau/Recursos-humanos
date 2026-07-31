<?php

// ==================== app/Models/HorarioDia.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HorarioDia extends Model
{
    use HasFactory;
    protected $table = 'horario_dias';
    protected $fillable = ['horario_id', 'dia_semana', 'hora_entrada', 'hora_salida', 'hora_entrada_tarde', 'hora_salida_tarde'];
    protected $casts = [
        'dia_semana' => 'integer',
        'hora_entrada' => 'datetime:H:i:s',
        'hora_salida' => 'datetime:H:i:s',
        'hora_entrada_tarde' => 'datetime:H:i:s',
        'hora_salida_tarde' => 'datetime:H:i:s',
    ];

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    /**
     * Devuelve los checkpoints del día como array asociativo.
     */
    public function checkpoints(): array
    {
        $checkpoints = [];

        if ($this->hora_entrada) {
            $checkpoints['entrada_manana'] = $this->hora_entrada instanceof Carbon
                ? $this->hora_entrada->format('H:i:s')
                : $this->hora_entrada;
        }
        if ($this->hora_salida) {
            $checkpoints['salida_manana'] = $this->hora_salida instanceof Carbon
                ? $this->hora_salida->format('H:i:s')
                : $this->hora_salida;
        }
        if ($this->hora_entrada_tarde) {
            $checkpoints['entrada_tarde'] = $this->hora_entrada_tarde instanceof Carbon
                ? $this->hora_entrada_tarde->format('H:i:s')
                : $this->hora_entrada_tarde;
        }
        if ($this->hora_salida_tarde) {
            $checkpoints['salida_tarde'] = $this->hora_salida_tarde instanceof Carbon
                ? $this->hora_salida_tarde->format('H:i:s')
                : $this->hora_salida_tarde;
        }

        return $checkpoints;
    }



    public function esPartida(): bool
    {
        return !empty($this->hora_entrada_tarde) && !empty($this->hora_salida_tarde);
    }

}
