<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasHistorialBonos extends Model
{
    use HasFactory;

    protected $table = 'cas_historial_bonos';
    public $timestamps = false;

    protected $fillable = [
        'id_cas',
        'id_usuario',
        'porcentaje_bono_anterior',
        'porcentaje_bono_nuevo',
        'monto_bono_anterior',
        'monto_bono_nuevo',
        'id_salario_minimo_anterior',
        'id_salario_minimo_nuevo',
        'anios_servicio_anterior',
        'anios_servicio_nuevo',
        'meses_servicio_anterior',
        'meses_servicio_nuevo',
        'dias_servicio_anterior',
        'dias_servicio_nuevo',
        'tipo_cambio',
        'observacion',
        'fecha_cambio'
    ];

    protected $casts = [
        'porcentaje_bono_anterior' => 'decimal:2',
        'porcentaje_bono_nuevo' => 'decimal:2',
        'monto_bono_anterior' => 'decimal:2',
        'monto_bono_nuevo' => 'decimal:2',
        'fecha_cambio' => 'datetime'
    ];

    // RELACIONES
    public function cas()
    {
        return $this->belongsTo(Cas::class, 'id_cas');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function salarioMinimoAnterior()
    {
        return $this->belongsTo(ConfiguracionSalarioMinimo::class, 'id_salario_minimo_anterior');
    }

    public function salarioMinimoNuevo()
    {
        return $this->belongsTo(ConfiguracionSalarioMinimo::class, 'id_salario_minimo_nuevo');
    }

    // SCOPES PARA FILTRAR
    public function scopePorTipoCambio($query, $tipo)
    {
        return $query->where('tipo_cambio', $tipo);
    }

    public function scopePorCas($query, $idCas)
    {
        return $query->where('id_cas', $idCas);
    }

    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_cambio', [$fechaInicio, $fechaFin]);
    }

    public function scopeConSalarioMinimo($query, $idSalario)
    {
        return $query->where(function($q) use ($idSalario) {
            $q->where('id_salario_minimo_anterior', $idSalario)
              ->orWhere('id_salario_minimo_nuevo', $idSalario);
        });
    }

    // MÉTODOS DE UTILIDAD
    public function huboCambioPorcentaje(): bool
    {
        return $this->porcentaje_bono_anterior != $this->porcentaje_bono_nuevo;
    }

    public function huboCambioMonto(): bool
    {
        return $this->monto_bono_anterior != $this->monto_bono_nuevo;
    }

    public function huboCambioSalario(): bool
    {
        return $this->id_salario_minimo_anterior != $this->id_salario_minimo_nuevo;
    }

    public function getDiferenciaMontoAttribute()
    {
        return ($this->monto_bono_nuevo ?? 0) - ($this->monto_bono_anterior ?? 0);
    }

    public function getDiferenciaPorcentajeAttribute()
    {
        return ($this->porcentaje_bono_nuevo ?? 0) - ($this->porcentaje_bono_anterior ?? 0);
    }

    public function getDescripcionCambioAttribute(): string
    {
        $descripciones = [
            'inicial' => 'Registro inicial del bono',
            'antiguedad' => 'Cambio por antigüedad',
            'salario' => 'Cambio por salario mínimo',
            'ambos' => 'Cambio por antigüedad y salario',
            'ajuste' => 'Ajuste manual del bono'
        ];

        return $descripciones[$this->tipo_cambio] ?? 'Cambio no especificado';
    }

    // MÉTODO PARA REGISTRAR CAMBIO DESDE EL MODELO CAS
    public static function registrarCambio(array $datos): self
    {
        return self::create([
            'id_cas' => $datos['id_cas'],
            'id_usuario' => $datos['id_usuario'] ?? auth()->id(),
            'porcentaje_bono_anterior' => $datos['porcentaje_anterior'] ?? null,
            'porcentaje_bono_nuevo' => $datos['porcentaje_nuevo'] ?? null,
            'monto_bono_anterior' => $datos['monto_anterior'] ?? null,
            'monto_bono_nuevo' => $datos['monto_nuevo'] ?? null,
            'id_salario_minimo_anterior' => $datos['id_salario_minimo_anterior'] ?? null,
            'id_salario_minimo_nuevo' => $datos['id_salario_minimo_nuevo'] ?? null,
            'anios_servicio_anterior' => $datos['anios_servicio_anterior'] ?? null,
            'anios_servicio_nuevo' => $datos['anios_servicio_nuevo'] ?? null,
            'meses_servicio_anterior' => $datos['meses_servicio_anterior'] ?? null,
            'meses_servicio_nuevo' => $datos['meses_servicio_nuevo'] ?? null,
            'dias_servicio_anterior' => $datos['dias_servicio_anterior'] ?? null,
            'dias_servicio_nuevo' => $datos['dias_servicio_nuevo'] ?? null,
            'tipo_cambio' => $datos['tipo_cambio'] ?? 'ajuste',
            'observacion' => $datos['observacion'] ?? null,
            'fecha_cambio' => now()
        ]);
    }

    // MÉTODO PARA OBTENER HISTORIAL DE UN CAS
    public static function obtenerHistorialPorCas(int $idCas, array $filtros = [])
    {
        $query = self::with(['usuario', 'salarioMinimoAnterior', 'salarioMinimoNuevo'])
                    ->where('id_cas', $idCas)
                    ->orderBy('fecha_cambio', 'desc');

        // Aplicar filtros
        if (!empty($filtros['tipo_cambio'])) {
            $query->where('tipo_cambio', $filtros['tipo_cambio']);
        }

        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query->whereBetween('fecha_cambio', [
                $filtros['fecha_inicio'],
                $filtros['fecha_fin']
            ]);
        }

        return $query->get();
    }

    // MÉTODO PARA GENERAR REPORTE DE CAMBIOS
    public static function generarReporteCambios(array $filtros = [])
    {
        $query = self::with([
            'cas.persona',
            'salarioMinimoAnterior',
            'salarioMinimoNuevo',
            'usuario'
        ]);

        // Filtros
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query->whereBetween('fecha_cambio', [
                $filtros['fecha_inicio'],
                $filtros['fecha_fin']
            ]);
        }

        if (!empty($filtros['tipo_cambio'])) {
            $query->where('tipo_cambio', $filtros['tipo_cambio']);
        }

        if (!empty($filtros['id_persona'])) {
            $query->whereHas('cas', function($q) use ($filtros) {
                $q->where('id_persona', $filtros['id_persona']);
            });
        }

        return $query->orderBy('fecha_cambio', 'desc')->get();
    }
}
