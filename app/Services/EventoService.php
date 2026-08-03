<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\User;
use Carbon\Carbon;

class EventoService
{
    /**
     * Devuelve todo el contexto visual para mostrar hoy
     */
    public function obtenerContexto(User $user): array
    {
        $contexto = [
            'mostrar_bienvenida' => false,
            'es_cumpleanos' => false,
            'evento' => null,
            'tema' => [
                'tipo' => 'default',
                'mensaje' => null,
                'color_primario' => '#3B82F6',
                'color_secundario' => '#1E40AF',
                'icono' => null,
                'efecto' => null,
            ]
        ];

        // 1. PRIMER INGRESO (prioridad máxima, solo se muestra una vez)
        if (!$user->bienvenida_vista) {
            $contexto['mostrar_bienvenida'] = true;
            $contexto['tema'] = [
                'tipo' => 'bienvenida',
                'mensaje' => "¡Bienvenido al Sistema de Recursos Humanos, {$user->name}! 🎉",
                'color_primario' => '#6366F1',
                'color_secundario' => '#4F46E5',
                'icono' => '👋',
                'efecto' => 'confeti',
            ];
            return $contexto;
        }

        // 2. CUMPLEAÑOS (prioridad sobre eventos institucionales)
        $persona = $user->persona;
        if ($persona && $persona->fechaNacimiento) {
            $fechaNac = Carbon::parse($persona->fechaNacimiento);
            if ($fechaNac->format('m-d') === now()->format('m-d')) {
                $nombre = trim("{$persona->nombre} {$persona->apellidoPat}");
                $contexto['es_cumpleanos'] = true;
                $contexto['tema'] = [
                    'tipo' => 'cumpleanos',
                    'mensaje' => "¡Feliz Cumpleaños, {$nombre}! 🎂🎈",
                    'color_primario' => '#F59E0B',
                    'color_secundario' => '#D97706',
                    'icono' => '🎂',
                    'efecto' => 'confeti',
                ];
                return $contexto;
            }
        }

        // 3. EVENTO ESPECIAL (6 de Agosto, Navidad, etc.)
        $evento = Evento::hoy()->first();
        if ($evento) {
            $contexto['evento'] = $evento;
            $contexto['tema'] = [
                'tipo' => $evento->tipo,
                'mensaje' => $evento->mensaje,
                'color_primario' => $evento->color_primario,
                'color_secundario' => $evento->color_secundario,
                'icono' => $evento->icono,
                'efecto' => $evento->efecto,
            ];
        }

        return $contexto;
    }

    /**
     * Marca la bienvenida como vista para no volver a mostrarla
     */
    public function marcarBienvenidaVista(User $user): void
    {
        $user->update(['bienvenida_vista' => true]);
    }
}
