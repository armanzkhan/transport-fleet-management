<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-audit-logs');
        
        $query = AuditLog::with(['user']);
        
        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Get filter options
        $users = User::where('is_active', true)->orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $modelTypes = AuditLog::select('model_type')->distinct()->orderBy('model_type')->pluck('model_type');
        
        // Statistics
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditLog::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
        
        // Top users by activity
        $topUsers = AuditLog::select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();
        
        // Activity by action type
        $activityByAction = AuditLog::select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('audit-logs.index', compact(
            'auditLogs',
            'users',
            'actions',
            'modelTypes',
            'stats',
            'topUsers',
            'activityByAction'
        ));
    }
    
    public function show(AuditLog $auditLog)
    {
        $this->authorize('view-audit-logs');
        
        $auditLog->load(['user']);
        
        // Try to load the related model
        $relatedModel = null;
        if ($auditLog->model_type && $auditLog->model_id) {
            try {
                $relatedModel = $auditLog->model_type::find($auditLog->model_id);
            } catch (\Exception $e) {
                // Model might not exist anymore
            }
        }
        
        return view('audit-logs.show', compact('auditLog', 'relatedModel'));
    }
    
    public function export(Request $request)
    {
        $this->authorize('export-audit-logs');
        
        $query = AuditLog::with(['user']);
        
        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $auditLogs = $query->orderBy('created_at', 'desc')->get();
        
        $exportType = $request->get('type', 'csv');
        
        $printService = new \App\Services\PrintExportService();
        
        $columns = [
            'Date/Time' => 'created_at',
            'User' => 'user.name',
            'Action' => 'action',
            'Model Type' => 'model_type',
            'Model ID' => 'model_id',
            'IP Address' => 'ip_address',
        ];
        
        $exportData = $auditLogs->map(function($log) {
            return [
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'user_name' => $log->user->name ?? 'System',
                'action' => $log->action,
                'model_type' => class_basename($log->model_type),
                'model_id' => $log->model_id,
                'ip_address' => $log->ip_address ?? 'N/A',
            ];
        });
        
        if ($exportType === 'word') {
            $result = $printService->generateWord($exportData, 'Audit Log Export', $columns);
        } elseif ($exportType === 'html') {
            $result = $printService->generateHTML($exportData, 'Audit Log Export', $columns);
        } else {
            $result = $printService->generateCSV($exportData, 'Audit Log Export', $columns);
        }
        
        return response()->download($result['file'], $result['filename'], [
            'Content-Type' => $result['mime']
        ])->deleteFileAfterSend(true);
    }
}

