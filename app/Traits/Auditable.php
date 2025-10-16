<?php
// app/Traits/Auditable.php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->logAuditEvent('create', 'Registro creado', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $model->logAuditEvent('update', 'Registro actualizado', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            $model->logAuditEvent('delete', 'Registro eliminado', $model->getOriginal(), null);
        });
    }

    public function logAuditEvent(string $event, string $description, $oldValues = null, $newValues = null)
    {
        if (Auth::check()) {
            AuditLog::create([
                'event' => $event,
                'model_type' => get_class($this),
                'model_id' => $this->getKey(),
                'old_values' => $oldValues ? $this->hideSensitiveData($oldValues) : null,
                'new_values' => $newValues ? $this->hideSensitiveData($newValues) : null,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function logCustomEvent(string $event, string $description, $extraData = null)
    {
        if (Auth::check()) {
            AuditLog::create([
                'event' => $event,
                'model_type' => get_class($this),
                'model_id' => $this->getKey(),
                'old_values' => null,
                'new_values' => $extraData,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'user_id' => Auth::id(),
            ]);
        }
    }

    protected function hideSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_token',
            'remember_token',
            'secret',
            'credit_card',
            'cvv'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***HIDDEN***';
            }
        }

        return $data;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable', 'model_type', 'model_id');
    }
}
