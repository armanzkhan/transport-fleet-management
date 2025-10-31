@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Audit Logs</h2>
                <p class="text-muted mb-0">View system activity and user actions.</p>
            </div>
            <div>
                <a href="{{ route('audit-logs.export', array_merge(request()->query(), ['type' => 'csv'])) }}" class="btn btn-outline-success">
                    <i class="fas fa-file-csv me-2"></i>
                    Export CSV
                </a>
                <a href="{{ route('audit-logs.export', array_merge(request()->query(), ['type' => 'html'])) }}" class="btn btn-outline-info">
                    <i class="fas fa-file-code me-2"></i>
                    Export HTML
                </a>
                <a href="{{ route('audit-logs.export', array_merge(request()->query(), ['type' => 'word'])) }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-word me-2"></i>
                    Export Word
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Logs</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_logs']) }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Today</h6>
                        <h3 class="mb-0">{{ number_format($stats['today_logs']) }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">This Week</h6>
                        <h3 class="mb-0">{{ number_format($stats['this_week']) }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">This Month</h6>
                        <h3 class="mb-0">{{ number_format($stats['this_month']) }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Search logs...">
            </div>
            <div class="col-md-2">
                <label for="user_id" class="form-label">User</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="action" class="form-label">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="model_type" class="form-label">Model Type</label>
                <select class="form-select" id="model_type" name="model_type">
                    <option value="">All Types</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                            {{ class_basename($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Apply Filters
                </button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Top Users and Activity -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Users by Activity</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th class="text-end">Activities</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUsers as $topUser)
                                <tr>
                                    <td>{{ $topUser->user->name ?? 'Unknown' }}</td>
                                    <td class="text-end">{{ number_format($topUser->activity_count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Activity by Action Type</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityByAction as $activity)
                                <tr>
                                    <td>{{ ucfirst($activity->action) }}</td>
                                    <td class="text-end">{{ number_format($activity->count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>
            Audit Logs ({{ $auditLogs->total() }} total)
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <div>{{ $log->user->name ?? 'System' }}</div>
                                <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action === 'create' ? 'success' : ($log->action === 'update' ? 'warning' : ($log->action === 'delete' ? 'danger' : 'info')) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <code>{{ class_basename($log->model_type) }}</code>
                            </td>
                            <td>{{ $log->model_id ?? 'N/A' }}</td>
                            <td>
                                <small>{{ $log->ip_address ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No audit logs found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection

