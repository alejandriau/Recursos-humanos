<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'tipo', 'fecha', 'es_recurrente', 'mensaje',
        'color_primario', 'color_secundario', 'icono', 'efecto', 'activo'
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_recurrente' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Eventos que ocurren hoy (comparando solo mes-día)
     */
    public function scopeHoy($query)
    {
        $hoy = Carbon::now()->format('m-d');
        return $query->whereRaw("DATE_FORMAT(fecha, '%m-%d') = ?", [$hoy])
                     ->where('activo', true);
    }
}
