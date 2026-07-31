<?php
// Repartir cada clase en su propio archivo dentro de app/Models/

// ==================== app/Models/Horario.php ====================
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Horario extends Model
{
    use HasFactory;
    protected $table = 'horarios';
    protected $fillable = ['nombre', 'descripcion', 'tolerancia_entrada_minutos', 'tolerancia_salida_minutos', 'activo'];
        protected $casts = [
        'tolerancia_entrada_minutos' => 'integer',
        'tolerancia_salida_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    public function dias(): HasMany
    {
        return $this->hasMany(HorarioDia::class, 'horario_id');
    }

    public function personaHorarios(): HasMany
    {
        return $this->hasMany(PersonaHorario::class, 'horario_id');
    }



    public function getResumenHorarioAttribute()
    {
        if ($this->dias->isEmpty()) {
            return 'Sin días configurados';
        }

        $diasOrdenados = $this->dias->sortBy('dia_semana');
        $nombresDias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $partes = [];

        foreach ($diasOrdenados as $dia) {
            $nombre = $nombresDias[$dia->dia_semana];
            $horario = $dia->hora_entrada . ' - ' . $dia->hora_salida;
            if ($dia->hora_entrada_tarde && $dia->hora_salida_tarde) {
                $horario .= ' / ' . $dia->hora_entrada_tarde . ' - ' . $dia->hora_salida_tarde;
            }
            $partes[] = $nombre . ' ' . $horario;
        }

        return implode(', ', $partes);
    }
}
