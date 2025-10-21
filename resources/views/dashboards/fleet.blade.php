@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Fleet Management Dashboard</h2>
                <p class="text-muted mb-0">
                    Vehicle operations and fleet management for {{ Auth::user()->name }}.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#fleetSettingsModal">
                    <i class="fas fa-cog me-2"></i>
                    Fleet Settings
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-2"></i>
                        Export Fleet Report
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

    <!-- Fleet Overview Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Vehicles</div>
                        <div class="h4 mb-0">{{ $stats['total_vehicles'] ?? 0 }}</div>
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
                        <i class="fas fa-route fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Active Journeys</div>
                        <div class="h4 mb-0">{{ $stats['active_journeys'] ?? 0 }}</div>
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
                        <i class="fas fa-check-circle fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Completed Journeys</div>
                        <div class="h4 mb-0">{{ $stats['processed_journeys'] ?? 0 }}</div>
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
                        <div class="text-muted small">Maintenance Due</div>
                        <div class="h4 mb-0">{{ $stats['maintenance_due'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Status Overview -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Vehicle Status Overview</h5>
                <p class="text-muted small mb-0">Current status of all vehicles in the fleet.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Status</th>
                                <th>Last Journey</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicleStatus as $vehicle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($vehicle->vrn, -2) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $vehicle->vrn }}</div>
                                            <small class="text-muted">{{ $vehicle->make }} {{ $vehicle->model }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $vehicle->driver_name }}</div>
                                    <small class="text-muted">{{ $vehicle->driver_phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $vehicle->is_active ? 'success' : 'danger' }}">
                                        {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $vehicle->last_journey_date ? $vehicle->last_journey_date->diffForHumans() : 'No journeys' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No vehicles found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fleet Alerts -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Fleet Alerts</h5>
                <p class="text-muted small mb-0">Important fleet notifications and warnings.</p>
            </div>
            <div class="card-body p-0">
                @forelse($fleetAlerts as $alert)
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
                    <div>No fleet alerts at this time</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Journey Activities -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Journey Activities</h5>
                <p class="text-muted small mb-0">Latest journey operations and vehicle movements.</p>
            </div>
            <div class="card-body p-0">
                @forelse($recentJourneys as $journey)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <i class="fas fa-route text-primary fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-medium">{{ $journey->vehicle->vrn }} - {{ $journey->route }}</div>
                        <div class="text-muted small">
                            Driver: {{ $journey->driver_name }} | 
                            Status: {{ $journey->is_processed ? 'Completed' : 'In Progress' }}
                        </div>
                    </div>
                    <div class="text-muted small">{{ $journey->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    No recent journey activities found.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Fleet Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Quick Actions</h5>
                <p class="text-muted small mb-0">Common fleet management tasks.</p>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('vehicles.create') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-plus me-2"></i>
                        Add Vehicle
                    </a>
                    
                    <a href="{{ route('journey-vouchers.primary') }}" class="btn btn-outline-success text-start">
                        <i class="fas fa-route me-2"></i>
                        Create Journey
                    </a>
                    
                    <a href="{{ route('vehicles.index', ['filter' => 'expiring']) }}" class="btn btn-outline-warning text-start">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Expiring Documents
                    </a>
                    
                    <a href="{{ route('reports.vehicle-database') }}" class="btn btn-outline-info text-start">
                        <i class="fas fa-chart-bar me-2"></i>
                        Fleet Reports
                    </a>
                    
                    <button type="button" class="btn btn-outline-secondary text-start" data-bs-toggle="modal" data-bs-target="#fleetSettingsModal">
                        <i class="fas fa-cog me-2"></i>
                        Fleet Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Performance Chart -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Vehicle Performance Overview</h5>
                <p class="text-muted small mb-0">Monthly performance metrics for fleet vehicles.</p>
            </div>
            <div class="card-body">
                <canvas id="vehiclePerformanceChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Fleet Settings Modal -->
<div class="modal fade" id="fleetSettingsModal" tabindex="-1" aria-labelledby="fleetSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fleetSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>
                    Fleet Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fleetSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Vehicle Settings</h6>
                            <div class="mb-3">
                                <label for="maintenanceInterval" class="form-label">Maintenance Interval (Days)</label>
                                <input type="number" class="form-control" id="maintenanceInterval" value="90">
                            </div>
                            <div class="mb-3">
                                <label for="documentExpiryAlert" class="form-label">Document Expiry Alert (Days)</label>
                                <input type="number" class="form-control" id="documentExpiryAlert" value="15">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Journey Settings</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="autoProcessJourneys" checked>
                                <label class="form-check-label" for="autoProcessJourneys">
                                    Auto-process completed journeys
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="journeyNotifications" checked>
                                <label class="form-check-label" for="journeyNotifications">
                                    Enable journey notifications
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveFleetSettings()">
                    <i class="fas fa-save me-1"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Vehicle Performance Chart
const ctx = document.getElementById('vehiclePerformanceChart').getContext('2d');
const vehiclePerformanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Active Vehicles',
            data: [12, 19, 15, 25, 22, 30],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }, {
            label: 'Completed Journeys',
            data: [65, 59, 80, 81, 56, 95],
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function saveFleetSettings() {
    // Implementation for saving fleet settings
    alert('Fleet settings saved successfully!');
    const modal = bootstrap.Modal.getInstance(document.getElementById('fleetSettingsModal'));
    modal.hide();
}
</script>
@endpush
