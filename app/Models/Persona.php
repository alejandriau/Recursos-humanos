<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\memopuesto;
use App\Models\Historial;
use App\Models\Afps;
use App\Models\Cajacordes;
use App\Models\Puestos;


class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'id';
    public $timestamps = false; // porque no usas los campos created_at y updated_at típicos

    protected $fillable = [
        'id_servidor',
        'ci',
        'nombre',
        'apellidoPat',
        'apellidoMat',
        'fechaIngreso',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'observaciones',
        'estado',
        'foto',
        'archivo',
        'user_id',
        'fechaRegistro',
        'fechaActualizacion'
    ];

    protected $casts = [
        'fechaRegistro' => 'date',
        'fechaActualizacion' => 'date',
        'fechaIngreso' => 'date',
        'fechaNacimiento' => 'date',
        'estado' => 'boolean',
    ];
    //crear usuario para persona--------------------------------------
    protected static function booted()
    {
        // Al crear persona, crear usuario
        static::created(function ($persona) {
            $persona->crearUsuario();
        });

        // Al actualizar, actualizar usuario si cambian datos relevantes
        static::updated(function ($persona) {
            if ($persona->isDirty(['ci', 'nombre', 'apellidoPat', 'apellidoMat'])) {
                $persona->actualizarUsuario();
            }
        });

        // Al eliminar persona, eliminar usuario permanentemente (opcional)
        static::deleted(function ($persona) {
            if ($persona->user) {
                $persona->user->forceDelete();
            }
        });
    }

    // Relación
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Crear usuario nuevo
    public function crearUsuario()
    {
        if ($this->user_id) return;

        // Verificar que el CI no esté en uso (incluso en eliminados)
        if (User::withTrashed()->where('usuario', $this->ci)->exists()) {
            throw new \Exception("El CI {$this->ci} ya está registrado como usuario.");
        }

        $nombreCompleto = trim(
            ($this->nombre ?? '') . ' ' .
            ($this->apellidoPat ?? '') . ' ' .
            ($this->apellidoMat ?? '')
        );

        $user = User::create([
            'name'     => $nombreCompleto ?: 'Sin nombre',
            'email'    => null,
            'usuario'  => $this->ci,
            'ci'       => $this->ci,
            'password' => Hash::make($this->ci), // contraseña = CI
            'origen'   => 'local',
        ]);

        $user->assignRole('empleado');

        $this->user_id = $user->id;
        $this->save(); // guardar referencia
    }

    // Actualizar usuario existente
    public function actualizarUsuario()
    {
        if (!$this->user) return;

        $user = $this->user;

        // Actualizar nombre
        $nombreCompleto = trim(
            ($this->nombre ?? '') . ' ' .
            ($this->apellidoPat ?? '') . ' ' .
            ($this->apellidoMat ?? '')
        );
        $user->name = $nombreCompleto ?: 'Sin nombre';

        // Si cambió el CI
        if ($this->isDirty('ci')) {
            $existing = User::withTrashed()
                ->where('usuario', $this->ci)
                ->where('id', '!=', $user->id)
                ->first();

            if ($existing) {
                throw new \Exception("El nuevo CI {$this->ci} ya está registrado en otro usuario.");
            }

            $user->usuario = $this->ci;
            $user->ci = $this->ci;
        }

        $user->save();
    }

    // Método para dar de baja (desactivar usuario y cambiar estado)
    public function darDeBaja()
    {
        $this->estado = 0;
        $this->save();

        if ($this->user && !$this->user->trashed()) {
            $this->user->delete(); // soft delete
        }
    }

    // Método para reactivar
    public function reactivar()
    {
        $this->estado = 1;
        $this->save();

        if ($this->user && $this->user->trashed()) {
            $this->user->restore();
        }
    }
    //********** */

        public function vacaciones()
    {
        return $this->hasMany(Vacacion::class, 'idPersona');
    }
    public function profesiones()
    {
        return $this->hasMany(Profesion::class, 'idPersona');
    }
    public function profesion()
    {
        return $this->hasOne(Profesion::class, 'idPersona');
    }

    public function historial()
    {
        return $this->hasMany(Historial::class, 'persona_id'); // 👈 Usa el nombre real de la columna
    }
    public function historials()
    {
        return $this->hasMany(Historial::class, 'persona_id');
    }

    public function afps()
    {
        return $this->hasMany(Afps::class, 'idPersona');
    }
    public function cajacordes()
    {
        return $this->hasMany(Cajacordes::class, 'idPersona');
    }

    // Agregar esta relación
    public function cenvis()
    {
        return $this->hasMany(Cenvi::class, 'persona_id')
                    ->where('estado', 1)
                    ->orderBy('fecha', 'desc');
    }

    public function cenviMasReciente()
    {
        return $this->hasOne(Cenvi::class, 'persona_id')
                    ->where('estado', 1)
                    ->latest('fecha');
    }


    // Acceder al puesto actual
// En el modelo Persona
    public function puestoActual()
    {
        return $this->hasOne(Historial::class, 'persona_id')
                    ->where('estado', 'activo')
                    ->whereNotNull('fecha_inicio') // Asegurar que tenga fecha
                    ->latest('fecha_inicio');
    }
    public function bajasaltas()
    {
        return $this->hasMany(Bajasaltas::class, 'idPersona');
    }

    public function djbRenta()
    {
        return $this->hasMany(DjbRenta::class, 'idPersona');
    }
    public function bachilleres()
    {
        return $this->hasMany(Bachiller::class, 'idPersona');
    }
    public function formularios1()
    {
        return $this->hasMany(Formulario1::class, 'idPersona');
    }
    public function formularios2()
    {
        return $this->hasMany(Formulario2::class, 'idPersona');
    }
    public function consanguinidades()
    {
        return $this->hasMany(forconsangui::class, 'idPersona');
    }
    public function croquis()
    {
        return $this->hasMany(Croqui::class, 'idPersona');
    }
    public function cedulasIdentidad()
    {
        return $this->hasMany(CedulaIdentidad::class, 'idPersona');
    }
    public function certificadosNacimiento()
    {
        return $this->hasMany(CertNacimiento::class, 'idPersona');
    }
    public function licenciasConducir()
    {
        return $this->hasMany(LicenciaConducir::class, 'idPersona');
    }
    public function licenciasMilitar()
    {
        return $this->hasMany(LicenciaMilitar::class, 'idPersona');
    }
    public function curriculums()
    {
        return $this->hasMany(Curriculum::class, 'idPersona');
    }
    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'idPersona');
    }

    public function historialPuestos()
    {
        return $this->hasMany(Historial::class, 'persona_id');
    }

    /**
     * Relación con el puesto actual (historial activo más reciente)
     */


    /**
     * Relación con todos los puestos activos
     */
    public function puestosActivos()
    {
        return $this->hasMany(Historial::class, 'persona_id')
            ->where('estado', 'activo');
    }

    /**
     * Obtener el historial ordenado por fecha más reciente
     */
    public function historialReciente()
    {
        return $this->hasMany(Historial::class, 'persona_id')
            ->orderBy('fecha_inicio', 'desc')
            ->orderBy('created_at', 'desc');
    }


    /// para reportes =============================
    public function historiales(): HasMany
    {
        return $this->hasMany(Historial::class, 'persona_id');
    }

    public function historialActivo(): HasOne
    {
        return $this->hasOne(Historial::class, 'persona_id')
            ->where('estado', 'activo')
            ->latest('fecha_inicio');
    }

    public function puestoActivo(): HasOne
    {
        return $this->hasOne(Historial::class, 'persona_id')
            ->where('estado', 'activo')
            ->latest('fecha_inicio')
            ->with('puesto');
    }

    // Scopes útiles
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeConPuestoActivo($query)
    {
        return $query->whereHas('historialActivo', function($q) {
            $q->where('estado', 'activo');
        });
    }

    // Accesores
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidoPat} {$this->apellidoMat}");
    }

    public function getEdadAttribute(): int
    {
        return $this->fechaNacimiento ? $this->fechaNacimiento->age : 0;
    }

    public function getAntiguedadAttribute(): int
    {
        return $this->fechaIngreso ? $this->fechaIngreso->diffInYears(now()) : 0;
    }

    // Nuevos accesores para relación con historial
    public function getPuestoActualAttribute()
    {
        return $this->historialActivo ? $this->historialActivo->puesto : null;
    }

    public function getUnidadActualAttribute()
    {
        return $this->puestoActual ? $this->puestoActual->unidadOrganizacional : null;
    }

    //aisistencias ================================
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'idPersona');
    }
    public function tieneAsistencia($fecha)
    {
        return $this->asistencias()->whereDate('fecha', $fecha)->exists();
    }

    //cass
    // Relación con el último CAS
    public function ultimoCas()
    {
        return $this->hasOne(Cas::class, 'id_persona')
                    ->where('estado_cas', 'vigente')
                    ->orderBy('fecha_calculo_antiguedad', 'desc');
    }

    // Relación con todos los CAS
    public function cas()
    {
        return $this->hasMany(Cas::class, 'id_persona')
                    ->orderBy('fecha_calculo_antiguedad', 'desc');
    }

        public function scopeActivas($query)
    {
        return $query->where('estado', '1');
    }
        // Scope para búsqueda por apellido
    public function scopePorApellido($query, $apellido)
    {
        return $query->where('apellidoPat', 'LIKE', "%{$apellido}%")
                    ->orWhere('apellidoMat', 'LIKE', "%{$apellido}%");
    }

    // Scope para búsqueda por nombre
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', 'LIKE', "%{$nombre}%");
    }

    // Scope para búsqueda por CI
    public function scopePorCi($query, $ci)
    {
        return $query->where('ci', 'LIKE', "%{$ci}%");
    }
    //cass
    public function casActual()
    {
        return $this->belongsTo(Cas::class, 'id_persona');
    }

    public function licenciaMilitar()
    {
        return $this->hasOne(LicenciaMilitar::class, 'idPersona');
    }

    public function cedula()
    {
        return $this->hasOne(CedulaIdentidad::class, 'idPersona');
    }
    //inmovilidades
        public function situacionesEspeciales()
    {
        return $this->hasMany(SituacionesEspeciales::class, 'persona_id');
    }

//inmovilidades


// 🔥 TU CÓDIGO (inmovilidades)
public function inmovilidades()
{
    return $this->hasManyThrough(
        InmovilidadesLaborales::class,
        SituacionesEspeciales::class,
        'persona_id',
        'situacion_id'
    );
}

public function inmovilidadesActivas()
{
    return $this->inmovilidades()
        ->where('estado', InmovilidadesLaborales::ESTADO_APROBADO)
        ->where('fecha_fin_inmovilidad', '>=', now());
}

// Verificar si tiene inmovilidad activa
public function tieneInmovilidadActiva()
{
    return $this->inmovilidadesActivas()->exists();
}

// Obtener inmovilidad activa actual
public function getInmovilidadActiva()
{
    return $this->inmovilidadesActivas()->first();
}

// 🔥 CÓDIGO DEL REPOSITORIO (selección + perfil)
public function selecciones()
{
    return $this->morphMany(Seleccion::class, 'carpeta');
}

/**
 * Obtener la profesión principal de la persona
 */
public function profesionPrincipal()
{
    return $this->hasOne(Profesion::class, 'idPersona')
                ->where('esPrincipal', true)
                ->where('estado', 1);
}

/**
 * Obtener todas las profesiones activas
 */
public function profesionesActivas()
{
    return $this->hasMany(Profesion::class, 'id_persona')
                ->where('estado', 1);
}

/**
 * Verificar si la persona cumple con los años de experiencia requeridos
 */
public function cumpleExperienciaRequerida(int $aniosRequeridos): bool
{
    $profesionPrincipal = $this->profesionPrincipal;

    if (!$profesionPrincipal || !$profesionPrincipal->fechaTitulo) {
        return false;
    }

    return $profesionPrincipal->aniosExperienciaDesdeTitulacion >= $aniosRequeridos;
}

/**
 * Verificar si la persona tiene título en provisión nacional
 */
public function tieneTituloProvisionNacional(): bool
{
    $profesionPrincipal = $this->profesionPrincipal;

    return $profesionPrincipal && $profesionPrincipal->tieneTituloProvision;
}

/**
 * Obtener el nivel académico más alto de la persona
 */
public function getNivelAcademicoMasAltoAttribute()
{
    return $this->profesionesActivas()
        ->with('carrera.nivelAcademico')
        ->get()
        ->sortByDesc(function ($profesion) {
            return $profesion->carrera->nivelAcademico->orden ?? 0;
        })
        ->first();
}
public function planillas()
{
    return $this->hasMany(Planilla::class);
}

    //beneficios asignar
    public function beneficios()
    {
        return $this->hasMany(BeneficioPeriodo::class);
    }
    //nuva integracion
    public function vacacionPeriodos()
    {
        return $this->hasMany(VacacionPeriodo::class, 'persona_id');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'persona_id');
    }
    //ASISTENCIA -------------------------
    public function horariosAsignados()
    {
        return $this->hasMany(PersonaHorario::class);
    }

    // Obtener el horario vigente en una fecha dada
    public function getHorarioVigente($fecha = null)
    {
        $fecha = $fecha ?: now()->toDateString();
        $asignacion = $this->horariosAsignados()
            ->where('fecha_inicio', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->where('fecha_fin', '>=', $fecha)
                  ->orWhereNull('fecha_fin');
            })
            ->first();
        return $asignacion ? $asignacion->horario : null;
    }
    public function asignaciones()
    {
        return $this->hasMany(PersonaHorario::class);
    }

    
}
