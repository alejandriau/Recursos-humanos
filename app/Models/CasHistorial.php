<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasHistorial extends Model
{
    use HasFactory;

    protected $table = 'cas_historial';

    // 🚫 Desactivar timestamps automáticos
    public $timestamps = false;

    protected $fillable = [
        'id_cas',
        'id_usuario',
        'estado_anterior',
        'estado_nuevo',
        'alerta_anterior',
        'alerta_nuevo',
        'observacion'
    ];

    // Relaciones
    public function cas()
    {
        return $this->belongsTo(Cas::class, 'id_cas');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Scope para historial reciente
    public function scopeReciente($query, $dias = 30)
    {
        return $query->where('fecha_registro', '>=', now()->subDays($dias));
    }
}
