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
            'es_cumpleanos'      => false,
            'evento'             => null,
            'mostrar_modal'      => false, // ← NUEVO
            'tema'               => [
                'tipo'             => 'default',
                'mensaje'          => null,
                'color_primario'   => '#3B82F6',
                'color_secundario' => '#1E40AF',
                'icono'            => null,
                'efecto'           => null,
            ]
        ];

        // 1. PRIMER INGRESO (prioridad máxima)
        if (!$user->bienvenida_vista) {
            $contexto['mostrar_bienvenida'] = true;
            $contexto['tema'] = [
                'tipo'             => 'bienvenida',
                'mensaje'          => "¡Bienvenido al Sistema de Recursos Humanos, {$user->name}! 🎉",
                'color_primario'   => '#6366F1',
                'color_secundario' => '#4F46E5',
                'icono'            => '👋',
                'efecto'           => 'confeti',
            ];
            $contexto['mostrar_modal'] = $this->registrarModalVisto('bienvenida');
            return $contexto;
        }

        // 2. CUMPLEAÑOS
        $persona = $user->persona;
        if ($persona && $persona->fechaNacimiento) {
            $fechaNac = Carbon::parse($persona->fechaNacimiento);
            if ($fechaNac->format('m-d') === now()->format('m-d')) {
                $nombre = trim("{$persona->nombre} {$persona->apellidoPat}");
                $contexto['es_cumpleanos'] = true;
                $contexto['tema'] = [
                    'tipo'             => 'cumpleanos',
                    'mensaje'          => "¡Feliz Cumpleaños, {$nombre}! 🎂🎈",
                    'color_primario'   => '#F59E0B',
                    'color_secundario' => '#D97706',
                    'icono'            => '🎂',
                    'efecto'           => 'confeti',
                ];
                $contexto['mostrar_modal'] = $this->registrarModalVisto('cumpleanos');
                return $contexto;
            }
        }

        // 3. EVENTO ESPECIAL (6 de Agosto, Navidad, etc.)
        $evento = Evento::hoy()->first();
        if ($evento) {
            $contexto['evento'] = $evento;
            $contexto['tema'] = [
                'tipo'             => $evento->tipo,
                'mensaje'          => $evento->mensaje,
                'color_primario'   => $evento->color_primario,
                'color_secundario' => $evento->color_secundario,
                'icono'            => $evento->icono,
                'efecto'           => $evento->efecto,
            ];
            // Usamos el ID del evento para la key de sesión
            $contexto['mostrar_modal'] = $this->registrarModalVisto('evento_' . $evento->id);
        }

        return $contexto;
    }

    /**
     * Verifica si ya se mostró este tipo de modal hoy.
     * Si no, lo marca en sesión y devuelve true.
     */
    private function registrarModalVisto(string $tipo): bool
    {
        $sessionKey = 'modal_visto_' . $tipo . '_' . now()->format('Y-m-d');

        if (session()->has($sessionKey)) {
            return false; // Ya se mostró hoy
        }

        session()->put($sessionKey, true);
        return true; // Se autoriza mostrar
    }

    /**
     * Marca la bienvenida como vista en BD (esto sigue igual)
     */
    public function marcarBienvenidaVista(User $user): void
    {
        $user->update(['bienvenida_vista' => true]);
    }
}