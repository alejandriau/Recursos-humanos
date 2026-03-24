<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seleccion extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'seleccions';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'carpeta_type',
        'carpeta_id',
        'registro',
        'tipo_seleccion',
        'user_id'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tipo_seleccion' => 'string', // Podrías crear un enum de PHP para esto
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtiene el modelo de carpeta asociado (relación polimórfica).
     */
    public function carpeta(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Obtiene el usuario que realizó la selección.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para filtrar por tipo de selección.
     */
    public function scopeTipoSeleccion($query, $tipo)
    {
        return $query->where('tipo_seleccion', $tipo);
    }

    /**
     * Scope para filtrar por tipo de carpeta.
     */
    public function scopePorCarpetaType($query, $tipo)
    {
        return $query->where('carpeta_type', $tipo);
    }

    /**
     * Scope para obtener selecciones de un usuario específico.
     */
    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Verifica si la selección es temporal.
     */
    public function esTemporal(): bool
    {
        return $this->tipo_seleccion === 'temporal';
    }

    /**
     * Verifica si la selección es préstamo directo.
     */
    public function esPrestamoDirecto(): bool
    {
        return $this->tipo_seleccion === 'prestamo_directo';
    }

    /**
     * Obtiene la clase del modelo de carpeta según el tipo.
     * Útil para resolver la clase concreta del modelo polimórfico.
     */
    public static function getCarpetaModelClass(string $carpetaType): string
    {
        return match($carpetaType) {
            'pasivouno' => Pasivouno::class,
            'pasivodos' => Pasivodos::class,
            'personal' => Persona::class,
            default => throw new \InvalidArgumentException("Tipo de carpeta no válido: {$carpetaType}")
        };
    }
    

}