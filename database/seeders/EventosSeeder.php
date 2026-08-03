<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventosSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [

            // ==========================
            // ENERO
            // ==========================
            [
                'nombre' => 'Año Nuevo',
                'tipo' => 'festivo',
                'fecha' => '2026-01-01',
                'mensaje' => '🎆 ¡Feliz Año Nuevo! Que este año esté lleno de éxitos.',
                'color_primario' => '#FFD700',
                'color_secundario' => '#FF9800',
                'icono' => '🎆',
                'efecto' => 'confeti',
            ],

            // ==========================
            // FEBRERO
            // ==========================
            [
                'nombre' => 'Carnaval',
                'tipo' => 'festivo',
                'fecha' => '2026-02-16',
                'mensaje' => '🎭 ¡Feliz Carnaval!',
                'color_primario' => '#9C27B0',
                'color_secundario' => '#E91E63',
                'icono' => '🎭',
                'efecto' => 'confeti',
            ],

            [
                'nombre' => 'Carnaval',
                'tipo' => 'festivo',
                'fecha' => '2026-02-17',
                'mensaje' => '🎭 ¡Feliz Carnaval!',
                'color_primario' => '#9C27B0',
                'color_secundario' => '#E91E63',
                'icono' => '🎭',
                'efecto' => 'confeti',
            ],

            // ==========================
            // MARZO
            // ==========================
            [
                'nombre' => 'Día del Mar',
                'tipo' => 'civico',
                'fecha' => '2026-03-23',
                'mensaje' => '🌊 Día del Mar. Recordemos nuestra historia.',
                'color_primario' => '#1565C0',
                'color_secundario' => '#42A5F5',
                'icono' => '🌊',
                'efecto' => 'olas',
            ],

            // ==========================
            // ABRIL
            // ==========================
            [
                'nombre' => 'Viernes Santo',
                'tipo' => 'religioso',
                'fecha' => '2026-04-03',
                'mensaje' => '✝️ Viernes Santo.',
                'color_primario' => '#795548',
                'color_secundario' => '#5D4037',
                'icono' => '✝️',
                'efecto' => 'brillo',
            ],

            // ==========================
            // MAYO
            // ==========================
            [
                'nombre' => 'Día del Trabajo',
                'tipo' => 'festivo',
                'fecha' => '2026-05-01',
                'mensaje' => '💼 Feliz Día del Trabajo.',
                'color_primario' => '#1565C0',
                'color_secundario' => '#0D47A1',
                'icono' => '💼',
                'efecto' => 'confeti',
            ],

            [
                'nombre' => 'Día de la Madre',
                'tipo' => 'conmemorativo',
                'fecha' => '2026-05-27',
                'mensaje' => '🌹 Feliz Día de la Madre.',
                'color_primario' => '#E91E63',
                'color_secundario' => '#F06292',
                'icono' => '🌹',
                'efecto' => 'flores',
            ],

            // ==========================
            // JUNIO
            // ==========================
            [
                'nombre' => 'Año Nuevo Andino Amazónico y del Chaco',
                'tipo' => 'festivo',
                'fecha' => '2026-06-21',
                'mensaje' => '☀️ Feliz Willka Kuti.',
                'color_primario' => '#FF9800',
                'color_secundario' => '#FFC107',
                'icono' => '☀️',
                'efecto' => 'sol',
            ],

            // ==========================
            // JULIO
            // ==========================
            [
                'nombre' => 'Día del Amigo',
                'tipo' => 'conmemorativo',
                'fecha' => '2026-07-23',
                'mensaje' => '🤝 Feliz Día del Amigo.',
                'color_primario' => '#E91E63',
                'color_secundario' => '#9C27B0',
                'icono' => '🤝',
                'efecto' => 'corazones',
            ],

            // ==========================
            // AGOSTO
            // ==========================
            [
                'nombre' => 'Independencia de Bolivia',
                'tipo' => 'festivo',
                'fecha' => '2026-08-06',
                'mensaje' => '🇧🇴 ¡Viva Bolivia!',
                'color_primario' => '#D32F2F',
                'color_secundario' => '#2E7D32',
                'icono' => '🇧🇴',
                'efecto' => 'banderas',
            ],

            // ==========================
            // SEPTIEMBRE
            // ==========================
            [
                'nombre' => 'Día del Estudiante',
                'tipo' => 'conmemorativo',
                'fecha' => '2026-09-21',
                'mensaje' => '📚 Feliz Día del Estudiante y de la Primavera.',
                'color_primario' => '#4CAF50',
                'color_secundario' => '#8BC34A',
                'icono' => '🌼',
                'efecto' => 'flores',
            ],

            // ==========================
            // NOVIEMBRE
            // ==========================
            [
                'nombre' => 'Todos Santos',
                'tipo' => 'religioso',
                'fecha' => '2026-11-02',
                'mensaje' => '🕊️ Día de Todos Santos.',
                'color_primario' => '#607D8B',
                'color_secundario' => '#455A64',
                'icono' => '🕊️',
                'efecto' => 'velas',
            ],

            // ==========================
            // DICIEMBRE
            // ==========================
            [
                'nombre' => 'Navidad',
                'tipo' => 'festivo',
                'fecha' => '2026-12-25',
                'mensaje' => '🎄 Feliz Navidad.',
                'color_primario' => '#C62828',
                'color_secundario' => '#2E7D32',
                'icono' => '🎄',
                'efecto' => 'nieve',
            ],
        ];

        foreach ($eventos as $evento) {
            Evento::create($evento);
        }
    }
}
