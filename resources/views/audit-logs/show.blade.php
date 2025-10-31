@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Audit Log Details</h2>
                <p class="text-muted mb-0">Detailed information about this system activity.</p>
            </div>
            <div>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Logs
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Main Log Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Log Information
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Date/Time:</th>
                        <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>User:</th>
                        <td>
                            <div>{{ $auditLog->user->name ?? 'System' }}</div>
                            <small class="text-muted">{{ $auditLog->user->email ?? 'N/A' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Action:</th>
                        <td>
                            <span class="badge bg-{{ $auditLog->action === 'create' ? 'success' : ($auditLog->action === 'update' ? 'warning' : ($auditLog->action === 'delete' ? 'danger' : 'info')) }} fs-6">
                                {{ ucfirst($auditLog->action) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Model Type:</th>
                        <td><code>{{ $auditLog->model_type }}</code></td>
                    </tr>
                    <tr>
                        <th>Model ID:</th>
                        <td>{{ $auditLog->model_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>IP Address:</th>
                        <td>{{ $auditLog->ip_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>User Agent:</th>
                        <td>
                            <small class="text-muted">{{ $auditLog->user_agent ?? 'N/A' }}</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Old Values -->
        @if($auditLog->old_values)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="mb-0">
                    <i class="fas fa-arrow-left me-2 text-warning"></i>
                    Old Values
                </h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if($auditLog->new_values)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success bg-opacity-10">
                <h5 class="mb-0">
                    <i class="fas fa-arrow-right me-2 text-success"></i>
                    New Values
                </h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Related Model -->
        @if($relatedModel)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info bg-opacity-10">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2 text-info"></i>
                    Related Model
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Type:</strong> {{ class_basename($relatedModel) }}
                </p>
                <p class="mb-2">
                    <strong>ID:</strong> {{ $relatedModel->id }}
                </p>
                <p class="mb-0">
                    <strong>Status:</strong> 
                    <span class="badge bg-success">Active</span>
                </p>
                
                @if(method_exists($relatedModel, 'getRouteKeyName'))
                    @php
                        $routeKey = $relatedModel->getRouteKeyName();
                    @endphp
                    @if($routeKey !== 'id')
                        <a href="{{ url('/' . str_replace('_', '-', Str::plural(Str::snake(class_basename($relatedModel)))) . '/' . $relatedModel->id) }}" 
                           class="btn btn-sm btn-outline-info mt-3">
                            <i class="fas fa-external-link-alt me-2"></i>
                            View Record
                        </a>
                    @endif
                @endif
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-secondary bg-opacity-10">
                <h5 class="mb-0">
                    <i class="fas fa-unlink me-2 text-secondary"></i>
                    Related Model
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">
                    The related model record no longer exists or cannot be loaded.
                </p>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('audit-logs.export', ['id' => $auditLog->id, 'type' => 'csv']) }}" class="btn btn-outline-success">
                        <i class="fas fa-file-csv me-2"></i>
                        Export as CSV
                    </a>
                    <a href="{{ route('audit-logs.export', ['id' => $auditLog->id, 'type' => 'html']) }}" class="btn btn-outline-info">
                        <i class="fas fa-file-code me-2"></i>
                        Export as HTML
                    </a>
                    <a href="{{ route('audit-logs.export', ['id' => $auditLog->id, 'type' => 'word']) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-word me-2"></i>
                        Export as Word
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

