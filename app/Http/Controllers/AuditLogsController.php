<?php
// app/Http/Controllers/AuditLogsController.php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuditLogsController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['event', 'user_id', 'date_from', 'date_to', 'model_type']);

        $auditLogs = AuditService::getAuditLogs($filters);

        // Estadísticas para el dashboard
        $stats = $this->getAuditStats($filters);
        $users = User::where('is_active', true)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'audit_logs' => $auditLogs,
                'filters' => $filters,
                'stats' => $stats
            ]);
        }

        return view('audit-logs.index', compact('auditLogs', 'filters', 'stats', 'users'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        if (request()->expectsJson()) {
            return response()->json([
                'audit_log' => $auditLog
            ]);
        }

        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Estadísticas detalladas por usuario
     */
    public function userStatistics(Request $request, $userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;
        $dateRange = $request->only(['date_from', 'date_to']);

        $stats = $this->getUserAuditStats($userId, $dateRange);
        $activityTimeline = $this->getUserActivityTimeline($userId, $dateRange);
        $mostActiveHours = $this->getMostActiveHours($userId, $dateRange);
        $frequentActions = $this->getFrequentActions($userId, $dateRange);

        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user,
                'stats' => $stats,
                'activity_timeline' => $activityTimeline,
                'most_active_hours' => $mostActiveHours,
                'frequent_actions' => $frequentActions
            ]);
        }

        $users = User::where('is_active', true)->get();

        return view('audit-logs.user-statistics', compact(
            'user', 'stats', 'activityTimeline', 'mostActiveHours',
            'frequentActions', 'users', 'dateRange'
        ));
    }

    /**
     * Dashboard de auditoría general
     */
    public function dashboard(Request $request)
    {
        $dateRange = $request->only(['date_from', 'date_to']);

        $globalStats = $this->getGlobalAuditStats($dateRange);
        $topUsers = $this->getTopActiveUsers($dateRange);
        $eventTrends = $this->getEventTrends($dateRange);
        $systemHealth = $this->getSystemHealthMetrics($dateRange);

        if ($request->expectsJson()) {
            return response()->json([
                'global_stats' => $globalStats,
                'top_users' => $topUsers,
                'event_trends' => $eventTrends,
                'system_health' => $systemHealth
            ]);
        }

        return view('audit-logs.dashboard', compact(
            'globalStats', 'topUsers', 'eventTrends', 'systemHealth', 'dateRange'
        ));
    }

    /**
     * Reporte de actividades sospechosas
     */
    public function suspiciousActivities(Request $request)
    {
        $dateRange = $request->only(['date_from', 'date_to']);
        $threshold = $request->get('threshold', 10); // Umbral para actividades sospechosas

        $suspiciousActivities = $this->detectSuspiciousActivities($dateRange, $threshold);

        if ($request->expectsJson()) {
            return response()->json([
                'suspicious_activities' => $suspiciousActivities,
                'threshold' => $threshold
            ]);
        }

        return view('audit-logs.suspicious-activities', compact('suspiciousActivities', 'threshold', 'dateRange'));
    }

    /**
     * Obtener lista de eventos únicos
     */
    public function getEvents()
    {
        $events = AuditLog::distinct()->pluck('event');

        return response()->json([
            'events' => $events
        ]);
    }

    /**
     * Obtener lista de tipos de modelo únicos
     */
    public function getModelTypes()
    {
        $modelTypes = AuditLog::distinct()->pluck('model_type');

        return response()->json([
            'model_types' => $modelTypes
        ]);
    }

    /**
     * Métodos privados para estadísticas
     */

    /**
     * Obtener estadísticas generales de auditoría
     */
    private function getAuditStats($filters = [])
    {
        $query = AuditLog::query();

        $this->applyFilters($query, $filters);

        return [
            'total_activities' => $query->count(),
            'today_activities' => AuditLog::whereDate('created_at', today())->count(),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'most_active_user' => $this->getMostActiveUser($filters),
            'events_distribution' => $this->getEventsDistribution($filters),
        ];
    }

    /**
     * Obtener el usuario más activo
     */
    private function getMostActiveUser($filters = [])
    {
        $query = AuditLog::select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc');

        $this->applyFilters($query, $filters);

        $mostActive = $query->first();

        return $mostActive ? [
            'user' => $mostActive->user->name ?? 'Usuario Eliminado',
            'count' => $mostActive->activity_count
        ] : null;
    }

    /**
     * Obtener distribución de eventos
     */
    private function getEventsDistribution($filters = [])
    {
        $query = AuditLog::select('event', DB::raw('COUNT(*) as count'))
            ->groupBy('event')
            ->orderBy('count', 'desc');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Obtener estadísticas de auditoría por usuario
     */
    private function getUserAuditStats($userId, $dateRange = [])
    {
        if (!$userId) {
            return [];
        }

        $query = AuditLog::where('user_id', $userId);
        $this->applyDateRange($query, $dateRange);

        $totalActivities = $query->count();
        $firstActivity = $query->oldest()->first();
        $lastActivity = $query->latest()->first();

        return [
            'total_activities' => $totalActivities,
            'first_activity' => $firstActivity ? $firstActivity->created_at : null,
            'last_activity' => $lastActivity ? $lastActivity->created_at : null,
            'activity_per_day' => $totalActivities > 0 ?
                round($totalActivities / max(1, $firstActivity->created_at->diffInDays(now())), 2) : 0,
            'events_breakdown' => $this->getUserEventsBreakdown($userId, $dateRange),
            'preferred_models' => $this->getUserPreferredModels($userId, $dateRange),
        ];
    }

    /**
     * Obtener desglose de eventos por usuario
     */
    private function getUserEventsBreakdown($userId, $dateRange = [])
    {
        return AuditLog::where('user_id', $userId)
            ->select('event', DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('event')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Obtener modelos preferidos por usuario
     */
    private function getUserPreferredModels($userId, $dateRange = [])
    {
        return AuditLog::where('user_id', $userId)
            ->select('model_type', DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->whereNotNull('model_type')
            ->groupBy('model_type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Obtener timeline de actividad del usuario
     */
    private function getUserActivityTimeline($userId, $dateRange = [])
    {
        if (!$userId) {
            return collect();
        }

        return AuditLog::where('user_id', $userId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange['date_from']), function($q) use ($dateRange) {
                $q->whereDate('created_at', '>=', $dateRange['date_from']);
            })
            ->when(!empty($dateRange['date_to']), function($q) use ($dateRange) {
                $q->whereDate('created_at', '<=', $dateRange['date_to']);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Obtener horas más activas del usuario
     */
    private function getMostActiveHours($userId, $dateRange = [])
    {
        if (!$userId) {
            return collect();
        }

        return AuditLog::where('user_id', $userId)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Obtener acciones frecuentes del usuario
     */
    private function getFrequentActions($userId, $dateRange = [])
    {
        if (!$userId) {
            return collect();
        }

        return AuditLog::where('user_id', $userId)
            ->select('event', 'model_type', DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('event', 'model_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener estadísticas globales de auditoría
     */
    private function getGlobalAuditStats($dateRange = [])
    {
        $query = AuditLog::query();
        $this->applyDateRange($query, $dateRange);

        return [
            'total_activities' => $query->count(),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'activities_today' => AuditLog::whereDate('created_at', today())->count(),
            'activities_this_week' => AuditLog::whereBetween('created_at',
                [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'peak_activity_hour' => $this->getPeakActivityHour($dateRange),
        ];
    }

    /**
     * Obtener hora pico de actividad
     */
    private function getPeakActivityHour($dateRange = [])
    {
        $peakHour = AuditLog::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        return $peakHour ? $peakHour->hour . ':00' : 'N/A';
    }

    /**
     * Obtener usuarios más activos
     */
    private function getTopActiveUsers($dateRange = [], $limit = 10)
    {
        return AuditLog::select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->with('user')
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener tendencias de eventos
     */
    private function getEventTrends($dateRange = [])
    {
        return AuditLog::select('event', DB::raw('COUNT(*) as count'))
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('event')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Obtener métricas de salud del sistema
     */
    private function getSystemHealthMetrics($dateRange = [])
    {
        $query = AuditLog::query();
        $this->applyDateRange($query, $dateRange);

        $totalActivities = $query->count();
        $errorActivities = clone $query;
        $warningActivities = clone $query;

        return [
            'total_activities' => $totalActivities,
            'error_rate' => $totalActivities > 0 ?
                round(($errorActivities->where('event', 'error')->count() / $totalActivities) * 100, 2) : 0,
            'warning_activities' => $warningActivities->whereIn('event', ['delete', 'failed_login'])->count(),
            'avg_activities_per_day' => $this->getAverageActivitiesPerDay($dateRange),
        ];
    }

    /**
     * Obtener promedio de actividades por día
     */
    private function getAverageActivitiesPerDay($dateRange = [])
    {
        $query = AuditLog::query();
        $this->applyDateRange($query, $dateRange);

        $totalActivities = $query->count();
        $days = $this->getDateRangeDays($dateRange);

        return $days > 0 ? round($totalActivities / $days, 2) : 0;
    }

    /**
     * Detectar actividades sospechosas
     */
    private function detectSuspiciousActivities($dateRange = [], $threshold = 10)
    {
        // Actividades fuera del horario laboral
        $afterHours = AuditLog::where(function($q) {
                $q->whereTime('created_at', '<', '08:00:00')
                  ->orWhereTime('created_at', '>', '18:00:00');
            })
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->with('user')
            ->get();

        // Usuarios con actividad excesiva
        $excessiveActivity = AuditLog::select('user_id', DB::raw('COUNT(*) as count'))
            ->with('user')
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->groupBy('user_id')
            ->having('count', '>', $threshold)
            ->get();

        // Múltiples eliminaciones en corto tiempo
        $massDeletions = AuditLog::where('event', 'delete')
            ->when(!empty($dateRange), function($q) use ($dateRange) {
                $this->applyDateRange($q, $dateRange);
            })
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->filter(function($logs) {
                return $logs->count() > 5; // Más de 5 eliminaciones
            });

        return [
            'after_hours' => $afterHours,
            'excessive_activity' => $excessiveActivity,
            'mass_deletions' => $massDeletions,
        ];
    }

    /**
     * Calcular días en el rango de fechas
     */
    private function getDateRangeDays($dateRange = [])
    {
        if (!empty($dateRange['date_from']) && !empty($dateRange['date_to'])) {
            $from = Carbon::parse($dateRange['date_from']);
            $to = Carbon::parse($dateRange['date_to']);
            return $from->diffInDays($to) + 1;
        }

        // Si no hay rango, asumir 30 días por defecto
        return 30;
    }

    /**
     * Aplicar filtros a la consulta
     */
    private function applyFilters($query, $filters)
    {
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        $this->applyDateRange($query, $filters);
    }

    /**
     * Aplicar rango de fechas
     */
    private function applyDateRange($query, $dateRange)
    {
        if (!empty($dateRange['date_from'])) {
            $query->whereDate('created_at', '>=', $dateRange['date_from']);
        }

        if (!empty($dateRange['date_to'])) {
            $query->whereDate('created_at', '<=', $dateRange['date_to']);
        }
    }
}
