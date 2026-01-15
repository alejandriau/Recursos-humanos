<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Certificado extends Model
{
    protected $fillable = [
        'nombre', 'tipo', 'categoria', 'fecha', 'fecha_vencimiento',
        'instituto', 'pdfcerts', 'idPersona', 'estado'
    ];

    protected $dates = ['fecha', 'fecha_vencimiento'];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    // Scope para filtrar certificados de quechua
    public function scopeQuechua($query)
    {
        return $query->where('categoria', 'quechua');
    }

    // Scope para filtrar certificados por vencer (en los próximos 30 días)
    public function scopePorVencer($query, $dias = 30)
    {
        return $query->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays($dias))
            ->whereDate('fecha_vencimiento', '>=', Carbon::now());
    }

    // Scope para filtrar certificados vencidos
    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<', Carbon::now());
    }

    // Scope para filtrar certificados sin vencimiento
    public function scopeSinVencimiento($query)
    {
        return $query->whereNull('fecha_vencimiento');
    }

    // Método para calcular fecha de vencimiento automáticamente
    public function calcularFechaVencimiento()
    {
        if ($this->categoria === 'quechua' && $this->fecha) {
            $this->fecha_vencimiento = Carbon::parse($this->fecha)->addYears(3);
        } else {
            // Otros certificados no vencen
            $this->fecha_vencimiento = null;
        }
    }
        public function isVencido()
    {
        if (!$this->fecha) return false;

        $fechaVencimiento = Carbon::parse($this->fecha)->addYears(3);
        return Carbon::now()->greaterThan($fechaVencimiento);
    }

    // Verificar si es certificado de quechua
    public function isQuechua()
    {
        return stripos($this->nombre, 'quechua') !== false ||
               stripos($this->categoria, 'quechua') !== false;
    }
}
