<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\EventoService;
use Symfony\Component\HttpFoundation\Response;

class ContextoEventoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Solo para usuarios autenticados
        if (Auth::check()) {
            $eventoService = app(EventoService::class);
            $contexto = $eventoService->obtenerContexto(Auth::user());
        } else {
            // Fallback para login/registro
            $contexto = [
                'mostrar_bienvenida' => false,
                'es_cumpleanos' => false,
                'evento' => null,
                'tema' => [
                    'tipo' => 'default',
                    'mensaje' => null,
                    'color_primario' => '#4DA3FF',
                    'color_secundario' => '#2F80ED',
                    'icono' => null,
                    'efecto' => null,
                    'clase_css' => 'theme-cochabamba',
                ]
            ];
        }

        // Compartir con TODAS las vistas de esta petición
        View::share('contextoEvento', $contexto);

        return $next($request);
    }
}
