<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    // Marcar como leídas SOLO las notificaciones de préstamos
    public function marcarPrestamosLeidas()
    {
        $user = Auth::user();
        
        // Tipos de notificaciones que queremos marcar
        $tiposPrestamo = [
            'nueva_solicitud',
            'prestamo_aprobado',
            'prestamo_rechazado',
            'prestamo_entregado',
            'prestamo_devuelto',
            'prestamo_vencido'
        ];
        
        $user->unreadNotifications->filter(function($notif) use ($tiposPrestamo) {
            return in_array($notif->data['tipo'] ?? '', $tiposPrestamo);
        })->each->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    // Marcar UNA notificación específica (ya existe en tu código)
    public function marcarLeida($id)
    {
        $notif = Auth::user()->notifications()->where('id', $id)->first();
        if ($notif) {
            $notif->markAsRead();
        }
        return response()->json(['success' => true]);
    }
    
    // Obtener todas las notificaciones del usuario (con paginación opcional)
    public function obtener()
    {
        $notifications = Auth::user()->notifications()->latest()->take(20)->get();
        return response()->json($notifications);
    }
}