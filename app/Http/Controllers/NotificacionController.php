<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Obtener NOTIFICACIONES NO LEÍDAS + conteo
     * Usado por el dropdown del navbar (Axios)
     */
    public function noLeidas()
    {
        $user = Auth::user();

        $notificaciones = $user->unreadNotifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'titulo' => $n->data['titulo'] ?? 'Notificación',
                    'mensaje' => $n->data['mensaje'] ?? '',
                    'tipo' => $n->data['tipo'] ?? 'info',    // cualquier tipo: vacacion, comision, prestamo, etc.
                    'url' => $n->data['url'] ?? '#',
                    'creado' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'notificaciones' => $notificaciones
        ]);
    }

    /**
     * Obtener TODAS las notificaciones (leídas y no leídas)
     * Útil para una página "Centro de Notificaciones"
     */
    public function todas(Request $request)
    {
        $notificaciones = Auth::user()->notifications()
            ->latest()
            ->paginate(20)
            ->through(function ($n) {
                return [
                    'id' => $n->id,
                    'titulo' => $n->data['titulo'] ?? 'Notificación',
                    'mensaje' => $n->data['mensaje'] ?? '',
                    'tipo' => $n->data['tipo'] ?? 'info',
                    'url' => $n->data['url'] ?? '#',
                    'leida' => !is_null($n->read_at),
                    'creado' => $n->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($notificaciones);
    }

    /**
     * Marcar UNA notificación como leída
     */
    public function marcarLeida($id)
    {
        $notif = Auth::user()->notifications()->where('id', $id)->first();

        if ($notif) {
            $notif->markAsRead();
        }

        return response()->json([
            'success' => true,
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marcar TODAS las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'count' => 0
        ]);
    }
}
