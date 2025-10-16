<?php
// app/Services/AuditService.php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(string $event, string $description, Model $model = null, $extraData = null)
    {
        if (Auth::check()) {
            $logData = [
                'event' => $event,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'user_id' => Auth::id(),
            ];

            if ($model) {
                $logData['model_type'] = get_class($model);
                $logData['model_id'] = $model->getKey();
            }

            if ($extraData) {
                $logData['new_values'] = $extraData;
            }

            AuditLog::create($logData);
        }
    }

    public static function logView(string $description, Model $model = null)
    {
        self::log('view', $description, $model);
    }

    public static function logDownload(string $description, Model $model = null)
    {
        self::log('download', $description, $model);
    }

    public static function logLogin(User $user)
    {
        self::log('login', 'Inicio de sesión exitoso', null, [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public static function logLogout(User $user)
    {
        self::log('logout', 'Cierre de sesión', null, [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public static function getAuditLogs($filters = [])
    {
        $query = AuditLog::with('user');

        if (isset($filters['event']) && $filters['event']) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['model_type']) && $filters['model_type']) {
            $query->where('model_type', $filters['model_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(25);
    }
}
