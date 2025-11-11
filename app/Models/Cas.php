<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cas extends Model
{
    use HasFactory;

    protected $table = 'cas';

    public $timestamps = false;

    protected $fillable = [
        'anios_servicio',
        'meses_servicio',
        'dias_servicio',
        'porcentaje_bono',
        'monto_bono',
        'fecha_ingreso_institucion',
        'fecha_emision_cas',
        'fecha_presentacion_rrhh',
        'fecha_calculo_antiguedad',
        'periodo_calificacion',
        'meses_calificacion',
        'archivo_cas',
        'estado_cas',
        'nivel_alerta',
        'aplica_bono_antiguedad',
        'rango_antiguedad',
        'observaciones',
        'id_persona',
        'id_usuario_registro',
        'id_escala_bono',
        'id_salario_minimo'
    ];
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';


    protected $casts = [
        'fecha_ingreso_institucion' => 'date',
        'fecha_emision_cas' => 'date',
        'fecha_presentacion_rrhh' => 'date',
        'fecha_calculo_antiguedad' => 'date',
        'porcentaje_bono' => 'decimal:2',
        'monto_bono' => 'decimal:2',
        'aplica_bono_antiguedad' => 'boolean'
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'id_usuario_registro');
    }

    public function escalaBono()
    {
        return $this->belongsTo(EscalaBonoAntiguedad::class, 'id_escala_bono');
    }

    public function salarioMinimo()
    {
        return $this->belongsTo(ConfiguracionSalarioMinimo::class, 'id_salario_minimo');
    }

    public function historial()
    {
        return $this->hasMany(CasHistorial::class, 'id_cas');
    }

    // Scopes
    public function scopeVigentes($query)
    {
        return $query->where('estado_cas', 'vigente');
    }

    public function scopeConAlerta($query, $nivelAlerta)
    {
        return $query->where('nivel_alerta', $nivelAlerta);
    }

    public function scopeAplicaBono($query)
    {
        return $query->where('aplica_bono_antiguedad', true);
    }

    public function scopePorPersona($query, $idPersona)
    {
        return $query->where('id_persona', $idPersona);
    }

    // Métodos de cálculo
    public function calcularAntiguedad()
    {
        $fechaIngreso = Carbon::parse($this->fecha_ingreso_institucion);
        $fechaCalculo = Carbon::parse($this->fecha_calculo_antiguedad);

        $diferencia = $fechaCalculo->diff($fechaIngreso);

        return [
            'anios' => $diferencia->y,
            'meses' => $diferencia->m,
            'dias' => $diferencia->d
        ];
    }

    public function calcularBonoAntiguedad()
    {
        $antiguedad = $this->calcularAntiguedad();
        $aniosServicio = $antiguedad['anios'];

        // Verificar si aplica bono (mínimo 2 años)
        $aplicaBono = $aniosServicio >= 2;

        if (!$aplicaBono) {
            return [
                'aplica_bono' => false,
                'porcentaje' => 0,
                'monto' => 0
            ];
        }

        // Buscar escala correspondiente
        $escala = EscalaBonoAntiguedad::encontrarPorAniosServicio($aniosServicio);
        $salarioVigente = ConfiguracionSalarioMinimo::obtenerSalarioVigente();

        if (!$escala || !$salarioVigente) {
            return [
                'aplica_bono' => true,
                'porcentaje' => 0,
                'monto' => 0,
                'error' => 'No se encontró escala de bono o salario vigente'
            ];
        }

        $montoBono = $salarioVigente->monto_salario_minimo * ($escala->porcentaje_bono / 100);

        return [
            'aplica_bono' => true,
            'escala' => $escala,
            'porcentaje' => $escala->porcentaje_bono,
            'monto' => $montoBono,
            'rango' => $escala->rango_texto
        ];
    }

    // Método para actualizar alertas
    public function actualizarAlerta()
    {
        $hoy = Carbon::now();
        $fechaCalculo = Carbon::parse($this->fecha_calculo_antiguedad);

        $diasDiferencia = $hoy->diffInDays($fechaCalculo, false);

        if ($diasDiferencia < -30) {
            $this->nivel_alerta = 'urgente'; // Rojo - Vencido hace más de 30 días
        } elseif ($diasDiferencia < 0) {
            $this->nivel_alerta = 'advertencia'; // Amarillo - Recién vencido
        } else {
            $this->nivel_alerta = 'normal'; // Verde - Vigente
        }

        $this->save();
    }

    //metodos para el historial bono
    private function determinarTipoCambio(array $anterior): string
    {
        $cambioPorcentaje = $anterior['porcentaje_bono'] != $this->porcentaje_bono;
        $cambioSalario = $anterior['id_salario_minimo'] != $this->id_salario_minimo;

        if ($cambioPorcentaje && $cambioSalario) return 'ambos';
        if ($cambioPorcentaje) return 'antiguedad';
        if ($cambioSalario) return 'salario';
        if ($anterior['porcentaje_bono'] === null) return 'inicial';
        return 'ajuste';
    }

    private function generarObservacionCambio(string $tipoCambio, array $anterior): string
    {
        switch ($tipoCambio) {
            case 'antiguedad':
                return "Cambio por antigüedad: {$anterior['anios_servicio']} a {$this->anios_servicio} años";

            case 'salario':
                $salarioAnterior = $anterior['id_salario_minimo']
                    ? ConfiguracionSalarioMinimo::find($anterior['id_salario_minimo'])->monto_salario_minimo
                    : 0;
                $salarioNuevo = $this->salarioMinimo->monto_salario_minimo ?? 0;
                return "Cambio por salario mínimo: {$salarioAnterior} a {$salarioNuevo} bs";

            case 'ambos':
                return "Cambio por antigüedad ({$anterior['anios_servicio']} a {$this->anios_servicio} años) y salario mínimo";

            case 'inicial':
                return "Cálculo inicial del bono por antigüedad";

            default:
                return "Ajuste de bono";
        }
    }

    private function registrarEnHistorialBonos(array $anterior): void
    {
        $tipoCambio = $this->determinarTipoCambio($anterior);

        CasHistorialBonos::registrarCambio([
            'id_cas' => $this->id,
            'id_usuario' => auth()->id(),
            'porcentaje_anterior' => $anterior['porcentaje_bono'],
            'porcentaje_nuevo' => $this->porcentaje_bono,
            'monto_anterior' => $anterior['monto_bono'],
            'monto_nuevo' => $this->monto_bono,
            'id_salario_minimo_anterior' => $anterior['id_salario_minimo'],
            'id_salario_minimo_nuevo' => $this->id_salario_minimo,
            'anios_servicio_anterior' => $anterior['anios_servicio'],
            'anios_servicio_nuevo' => $this->anios_servicio,
            'meses_servicio_anterior' => $anterior['meses_servicio'],
            'meses_servicio_nuevo' => $this->meses_servicio,
            'dias_servicio_anterior' => $anterior['dias_servicio'],
            'dias_servicio_nuevo' => $this->dias_servicio,
            'tipo_cambio' => $tipoCambio,
            'observacion' => $this->generarObservacionCambio($tipoCambio, $anterior)
        ]);
    }

    // MÉTODO PARA OBTENER HISTORIAL DE BONOS DE ESTE CAS
    public function obtenerHistorialBonos()
    {
        return CasHistorialBonos::obtenerHistorialPorCas($this->id);
    }


    public function historialBonos()
    {
        return $this->hasMany(CasHistorialBonos::class, 'id_cas');
    }
}
