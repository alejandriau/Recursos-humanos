<?php
// app/Models/AuditLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'user_id'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo('auditable', 'model_type', 'model_id');
    }

    /**
     * Obtener la clase CSS para el badge del evento
     */
    public function getEventBadge(): string
    {
        $badges = [
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'view' => 'info',
            'download' => 'primary',
            'login' => 'success',
            'logout' => 'secondary',
            'upload' => 'primary',
            'search' => 'info',
            'export' => 'success',
        ];

        return $badges[$this->event] ?? 'secondary';
    }

    /**
     * Obtener el nombre legible del evento
     */
    public function getEventName(): string
    {
        $names = [
            'create' => 'Crear',
            'update' => 'Actualizar',
            'delete' => 'Eliminar',
            'view' => 'Ver',
            'download' => 'Descargar',
            'login' => 'Iniciar Sesión',
            'logout' => 'Cerrar Sesión',
            'upload' => 'Subir',
            'search' => 'Buscar',
            'export' => 'Exportar',
        ];

        return $names[$this->event] ?? ucfirst($this->event);
    }

    /**
     * Obtener el nombre del modelo sin namespace
     */
    public function getModelBaseName(): string
    {
        if (!$this->model_type) {
            return 'N/A';
        }

        return class_basename($this->model_type);
    }

    /**
     * Scope para filtrar por evento
     */
    public function scopeEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
