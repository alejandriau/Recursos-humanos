<?php
// Repartir cada clase en su propio archivo dentro de app/Models/

// ==================== app/Models/Horario.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = ['nombre', 'descripcion', 'tolerancia_entrada_minutos', 'tolerancia_salida_minutos', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function dias()
    {
        return $this->hasMany(HorarioDia::class, 'horario_id');
    }
}

// ==================== app/Models/HorarioDia.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioDia extends Model
{
    protected $table = 'horario_dias';
    protected $fillable = ['horario_id', 'dia_semana', 'hora_entrada', 'hora_salida', 'hora_entrada_tarde', 'hora_salida_tarde'];

    public function esPartida(): bool
    {
        return !empty($this->hora_entrada_tarde) && !empty($this->hora_salida_tarde);
    }

    /**
     * Devuelve los checkpoints esperados del día como ['tipo' => 'HH:MM:SS']
     */
    public function checkpoints(): array
    {
        if ($this->esPartida()) {
            return [
                'entrada_manana' => $this->hora_entrada,
                'salida_manana'  => $this->hora_salida,
                'entrada_tarde'  => $this->hora_entrada_tarde,
                'salida_tarde'   => $this->hora_salida_tarde,
            ];
        }

        return [
            'entrada' => $this->hora_entrada,
            'salida'  => $this->hora_salida,
        ];
    }
}

// ==================== app/Models/PersonaHorario.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaHorario extends Model
{
    protected $table = 'persona_horarios';
    protected $fillable = ['persona_id', 'horario_id', 'fecha_inicio', 'fecha_fin', 'activo'];
    protected $casts = ['fecha_inicio' => 'date', 'fecha_fin' => 'date', 'activo' => 'boolean'];

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}

// ==================== app/Models/AsistenciaDiaria.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaDiaria extends Model
{
    protected $table = 'asistencia_diaria';
    protected $fillable = [
        'persona_id', 'fecha', 'horario_id', 'estado', 'minutos_tardanza',
        'total_marcas_esperadas', 'total_marcas_cumplidas',
        'total_marcas_justificadas', 'total_marcas_injustificadas', 'procesado_en',
    ];
    protected $casts = ['fecha' => 'date', 'procesado_en' => 'datetime'];

    public function marcas()
    {
        return $this->hasMany(AsistenciaMarca::class, 'asistencia_diaria_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}

// ==================== app/Models/AsistenciaMarca.php ====================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaMarca extends Model
{
    protected $table = 'asistencia_marcas';
    protected $fillable = [
        'asistencia_diaria_id', 'tipo_marca', 'hora_esperada', 'hora_real',
        'marcacion_id', 'estado', 'diferencia_minutos', 'salida_id',
    ];

    public function marcacion()
    {
        return $this->belongsTo(MarcacionBiometrica::class, 'marcacion_id');
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }
}
