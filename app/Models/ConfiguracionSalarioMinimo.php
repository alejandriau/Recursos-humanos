<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionSalarioMinimo extends Model
{
    use HasFactory;

    protected $table = 'configuracion_salario_minimo';
    public $timestamps = false;

    protected $fillable = [
        'gestion',
        'monto_salario_minimo',
        'fecha_vigencia',
        'vigente',
        'observaciones'
    ];

    protected $casts = [
        'monto_salario_minimo' => 'decimal:2',
        'vigente' => 'boolean',
        'fecha_vigencia' => 'date'
    ];

    // Relación con CAS
    public function cas()
    {
        return $this->hasMany(Cas::class, 'id_salario_minimo');
    }

    // Scope para salarios vigentes
    public function scopeVigente($query)
    {
        return $query->where('vigente', true);
    }

    // Obtener salario mínimo actual
    public static function obtenerSalarioVigente()
    {
        return static::vigente()->orderBy('fecha_vigencia', 'desc')->first();
    }
}
