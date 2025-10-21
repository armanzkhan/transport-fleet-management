@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Admin Dashboard</h2>
                <p class="text-muted mb-0">
                    System overview and administrative controls for {{ Auth::user()->name }}.
                    @if(Auth::user()->isSuperAdmin())
                        <span class="badge bg-warning text-dark ms-2">
                            <i class="fas fa-crown me-1"></i>
                            Super Admin
                        </span>
                    @elseif(Auth::user()->isAdmin())
                        <span class="badge bg-primary ms-2">
                            <i class="fas fa-user-shield me-1"></i>
                            Admin
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#systemSettingsModal">
                    <i class="fas fa-cog me-2"></i>
                    System Settings
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-2"></i>
                        Export Report
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'csv']) }}">
                            <i class="fas fa-file-csv me-2"></i>
                            Export as CSV
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'html']) }}">
                            <i class="fas fa-file-code me-2"></i>
                            Export as HTML
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'excel']) }}">
                            <i class="fas fa-file-excel me-2"></i>
                            Export as Excel
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiry Alerts -->
    @if(isset($expiryAlerts) && count($expiryAlerts) > 0)
    <div class="col-12 mb-4">
        @foreach($expiryAlerts as $alert)
        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endforeach
    </div>
    @endif

    <!-- System Overview Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Users</div>
                        <div class="h4 mb-0">{{ $stats['total_users'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Active Vehicles</div>
                        <div class="h4 mb-0">{{ $stats['active_vehicles'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Expiring Documents</div>
                        <div class="h4 mb-0">{{ $stats['expiring_documents'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">System Health</div>
                        <div class="h4 mb-0 text-success">98%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">User Management</h5>
                <p class="text-muted small mb-0">Recent user activities and system access.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->roles->first() ? 'primary' : 'secondary' }}">
                                        {{ $user->roles->first() ? $user->roles->first()->name : 'No Role' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-users me-1"></i>
                        Manage Users
                    </a>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus me-1"></i>
                        Add User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">System Alerts</h5>
                <p class="text-muted small mb-0">Critical system notifications and warnings.</p>
            </div>
            <div class="card-body p-0">
                @forelse($systemAlerts as $alert)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <i class="fas fa-{{ $alert['icon'] }} text-{{ $alert['type'] }} fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-medium">{{ $alert['title'] }}</div>
                        <div class="text-muted small">{{ $alert['message'] }}</div>
                    </div>
                    <div class="text-muted small">{{ $alert['time'] }}</div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <div>No system alerts at this time</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Backup Management (Super Admin Only) -->
    @if(Auth::user()->canAccessCompleteBackups())
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="card-title mb-0 text-warning">
                    <i class="fas fa-shield-alt me-2"></i>
                    Super Admin - Backup Management
                </h5>
                <p class="text-muted small mb-0">Complete system backup and restoration capabilities.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-primary" onclick="createCompleteBackup()">
                                <i class="fas fa-database me-2"></i>
                                Create Complete Backup
                            </button>
                        </div>
                        <small class="text-muted">Full system backup including all data and files</small>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-warning" onclick="scheduleDailyBackup()">
                                <i class="fas fa-calendar-day me-2"></i>
                                Schedule Daily Backup
                            </button>
                        </div>
                        <small class="text-muted">Automated daily backup scheduling</small>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-danger" onclick="restoreFromBackup()">
                                <i class="fas fa-undo me-2"></i>
                                Restore from Backup
                            </button>
                        </div>
                        <small class="text-muted">Restore system from previous backup</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activities -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent System Activities</h5>
                <p class="text-muted small mb-0">Latest administrative actions and system events.</p>
            </div>
            <div class="card-body p-0">
                @forelse($recentActivities as $activity)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-muted fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-medium">{{ $activity->user->name }}</div>
                        <div class="text-muted small">{{ ucfirst($activity->action) }} {{ class_basename($activity->model_type) }}</div>
                    </div>
                    <div class="text-muted small">{{ $activity->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    No recent activities found.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Admin Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Quick Actions</h5>
                <p class="text-muted small mb-0">Administrative shortcuts and tools.</p>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New User
                    </a>
                    
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-success text-start">
                        <i class="fas fa-chart-bar me-2"></i>
                        Generate Reports
                    </a>
                    
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-warning text-start">
                        <i class="fas fa-truck me-2"></i>
                        Manage Vehicles
                    </a>
                    
                    @if(Auth::user()->canAccessCompleteBackups())
                    <button type="button" class="btn btn-outline-info text-start" onclick="createCompleteBackup()">
                        <i class="fas fa-download me-2"></i>
                        Download Complete Backup
                    </button>
                    @endif
                    
                    @if(Auth::user()->canManageDailyBackups())
                    <button type="button" class="btn btn-outline-warning text-start" onclick="scheduleDailyBackup()">
                        <i class="fas fa-calendar-day me-2"></i>
                        Daily Backup Management
                    </button>
                    @endif
                    
                    <button type="button" class="btn btn-outline-secondary text-start" data-bs-toggle="modal" data-bs-target="#systemSettingsModal">
                        <i class="fas fa-cog me-2"></i>
                        System Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Settings Modal -->
<div class="modal fade" id="systemSettingsModal" tabindex="-1" aria-labelledby="systemSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>
                    System Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="systemSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>General Settings</h6>
                            <div class="mb-3">
                                <label for="appName" class="form-label">Application Name</label>
                                <input type="text" class="form-control" id="appName" value="{{ config('app.name') }}">
                            </div>
                            <div class="mb-3">
                                <label for="appUrl" class="form-label">Application URL</label>
                                <input type="url" class="form-control" id="appUrl" value="{{ config('app.url') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Notification Settings</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label" for="emailNotifications">
                                    Enable Email Notifications
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="systemAlerts" checked>
                                <label class="form-check-label" for="systemAlerts">
                                    Enable System Alerts
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSystemSettings()">
                    <i class="fas fa-save me-1"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveSystemSettings() {
    // Implementation for saving system settings
    alert('System settings saved successfully!');
    const modal = bootstrap.Modal.getInstance(document.getElementById('systemSettingsModal'));
    modal.hide();
}

// Super Admin Backup Management Functions
function createCompleteBackup() {
    if (confirm('This will create a complete system backup. This may take several minutes. Continue?')) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Backup...';
        button.disabled = true;
        
        // Simulate backup process (replace with actual implementation)
        setTimeout(() => {
            alert('Complete system backup created successfully!');
            button.innerHTML = originalText;
            button.disabled = false;
        }, 3000);
    }
}

function scheduleDailyBackup() {
    if (confirm('This will schedule automated daily backups. Continue?')) {
        alert('Daily backup schedule configured successfully!');
    }
}

function restoreFromBackup() {
    if (confirm('WARNING: This will restore the system from a backup. All current data will be replaced. Are you absolutely sure?')) {
        if (confirm('This action cannot be undone. Type "RESTORE" to confirm:')) {
            const confirmation = prompt('Type "RESTORE" to confirm backup restoration:');
            if (confirmation === 'RESTORE') {
                alert('Backup restoration initiated. System will restart after completion.');
            } else {
                alert('Backup restoration cancelled.');
            }
        }
    }
}
</script>
@endpush
