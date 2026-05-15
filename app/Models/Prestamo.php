<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Prestamo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prestamos';

    protected $fillable = [
        'carpeta_type',
        'carpeta_id',
        'solicitante_id',
        'archivero_id',
        'user_id',
        'estado',
        'fecha_solicitud',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'motivo_solicitud',
        'notas_archivero',
        'motivo_rechazo',
        'es_verbal'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_prestamo' => 'date',
        'fecha_devolucion_estimada' => 'date',
        'fecha_devolucion_real' => 'date',
        'es_verbal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        'prestado' => 'Prestado',
        'devuelto' => 'Devuelto',
        'vencido' => 'Vencido'
    ];

    const CARPETA_TYPES = [
        'App\Models\Pasivouno' => 'Pasivouno',
        'App\Models\Pasivodos' => 'Pasivodos',
        'App\Models\Persona' => 'Persona'
    ];
    protected $appends = [
    'fecha_solicitud_formateada',
    'fecha_prestamo_formateada',
    'fecha_devolucion_estimada_formateada',
    'fecha_devolucion_real_formateada'
];

    // Relación polimórfica
    public function carpeta()
    {
        return $this->morphTo();
    }

    // Relaciones con usuarios
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function archivero()
    {
        return $this->belongsTo(User::class, 'archivero_id');
    }

    public function usuarioRegistrador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['aprobado', 'prestado']);
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido')
            ->orWhere(function($q) {
                $q->where('estado', 'prestado')
                  ->whereNotNull('fecha_devolucion_estimada')
                  ->whereDate('fecha_devolucion_estimada', '<', Carbon::today());
            });
    }

    public function scopePorTipoCarpeta($query, $tipo)
    {
        return $query->where('carpeta_type', $tipo);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('solicitante_id', $usuarioId);
    }

    public function scopePorArchivero($query, $archiveroId)
    {
        return $query->where('archivero_id', $archiveroId);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_solicitud', [$fechaInicio, $fechaFin]);
    }

    // Métodos de ayuda
    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function isAprobado()
    {
        return $this->estado === 'aprobado';
    }

    public function isPrestado()
    {
        return $this->estado === 'prestado';
    }

    public function isDevuelto()
    {
        return $this->estado === 'devuelto';
    }

    public function isVencido()
    {
        return $this->estado === 'vencido';
    }

    public function isRechazado()
    {
        return $this->estado === 'rechazado';
    }

    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getCarpetaTypeLabelAttribute()
    {
        return self::CARPETA_TYPES[$this->carpeta_type] ?? $this->carpeta_type;
    }

    public function getDiasRetrasoAttribute()
    {
        if ($this->fecha_devolucion_real) {
            return 0;
        }

        if ($this->fecha_devolucion_estimada && $this->fecha_devolucion_estimada < Carbon::today()) {
            return Carbon::parse($this->fecha_devolucion_estimada)->diffInDays(Carbon::today());
        }

        return 0;
    }

    public function getEstaVencidoAttribute()
    {
        return !$this->fecha_devolucion_real && 
               $this->fecha_devolucion_estimada && 
               $this->fecha_devolucion_estimada < Carbon::today();
    }

    public function marcarComoVencido()
    {
        if ($this->estaVencido && $this->estado === 'prestado') {
            $this->estado = 'vencido';
            $this->save();
        }
    }

    protected static function booted()
    {
        static::saving(function ($prestamo) {
            // Actualizar estado automáticamente si está vencido
            if ($prestamo->estaVencido && $prestamo->estado === 'prestado') {
                $prestamo->estado = 'vencido';
            }
        });
    }
    public function getFechaSolicitudFormateadaAttribute()
{
    return $this->fecha_solicitud 
        ? $this->fecha_solicitud->translatedFormat('d M Y') 
        : 'N/A';
}

public function getFechaPrestamoFormateadaAttribute()
{
    return $this->fecha_prestamo 
        ? $this->fecha_prestamo->translatedFormat('d M Y') 
        : null;
}

public function getFechaDevolucionEstimadaFormateadaAttribute()
{
    return $this->fecha_devolucion_estimada 
        ? $this->fecha_devolucion_estimada->translatedFormat('d M Y') 
        : null;
}

public function getFechaDevolucionRealFormateadaAttribute()
{
    return $this->fecha_devolucion_real 
        ? $this->fecha_devolucion_real->translatedFormat('d M Y') 
        : null;
}
}