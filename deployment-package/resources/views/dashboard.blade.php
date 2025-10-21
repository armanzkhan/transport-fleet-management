@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Dashboard</h2>
                <p class="text-muted mb-0">
                    Welcome back, {{ Auth::user()->name }}! Here's what's happening with your fleet.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#notificationSettingsModal">
                    <i class="fas fa-bell me-2"></i>
                    Notification Settings
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

    <!-- Statistics Cards -->
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
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Owners</div>
                        <div class="h4 mb-0">{{ $stats['total_owners'] ?? 0 }}</div>
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
                        <i class="fas fa-route fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Journeys</div>
                        <div class="h4 mb-0">{{ $stats['total_journeys'] ?? 0 }}</div>
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
                        <i class="fas fa-file-invoice fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Bills</div>
                        <div class="h4 mb-0">{{ $stats['total_bills'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-specific Statistics -->
    @if(Auth::user()->isAccountant() || Auth::user()->isSuperAdmin())
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-book fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Cash Entries</div>
                        <div class="h4 mb-0">{{ $stats['total_cash_entries'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Pending Bills</div>
                        <div class="h4 mb-0">{{ $stats['pending_bills'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Finalized Bills</div>
                        <div class="h4 mb-0">{{ $stats['finalized_bills'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Expiring Documents Notifications -->
    @if($expiringVehicles->count() > 0)
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-header bg-warning bg-opacity-10 border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Document Expiry Alerts
                    </h5>
                    <span class="badge bg-warning">{{ $expiringVehicles->count() }} vehicles</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($expiringVehicles->take(6) as $vehicle)
                    @php
                        $expiringDocs = $vehicle->getExpiringDocuments();
                    @endphp
                    <div class="col-lg-6 mb-3">
                        <div class="card border border-warning bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 text-primary">{{ $vehicle->vrn }}</h6>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <p class="text-muted small mb-2">Driver: {{ $vehicle->driver_name }}</p>
                                
                                @foreach($expiringDocs as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-{{ $doc['type'] == 'Tracker' ? 'map-marker-alt' : ($doc['type'] == 'Dip Chart' ? 'chart-bar' : 'file-alt') }} me-1"></i>
                                        {{ $doc['type'] }}
                                    </span>
                                    <span class="badge bg-{{ $doc['days_remaining'] <= 3 ? 'danger' : ($doc['days_remaining'] <= 7 ? 'warning' : 'info') }}">
                                        @if($doc['days_remaining'] <= 0)
                                            Expired
                                        @elseif($doc['days_remaining'] == 1)
                                            Tomorrow
                                        @else
                                            {{ $doc['days_remaining'] }} days
                                        @endif
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing alerts for documents expiring within 15 days
                    </p>
                    <div>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-warning btn-sm me-2">
                            <i class="fas fa-truck me-1"></i>
                            View All Vehicles
                        </a>
                        <a href="{{ route('notifications') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-bell me-1"></i>
                            All Notifications
                        </a>
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
                <h5 class="card-title mb-0">Recent Activities</h5>
                <p class="text-muted small mb-0">Latest system activities and user actions.</p>
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

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Quick Actions</h5>
                <p class="text-muted small mb-0">Common tasks and shortcuts.</p>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('create-vehicles')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-plus me-2"></i>
                        Add Vehicle
                    </a>
                    @endcan

                    @can('create-vehicle-owners')
                    <a href="{{ route('vehicle-owners.create') }}" class="btn btn-outline-success text-start">
                        <i class="fas fa-user-plus me-2"></i>
                        Add Owner
                    </a>
                    @endcan

                    @can('create-cash-book')
                    <a href="{{ route('cash-books.receive') }}" class="btn btn-outline-warning text-start">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Cash Entry
                    </a>
                    @endcan

                    @can('create-journey-vouchers')
                    <a href="{{ route('journey-vouchers.primary') }}" class="btn btn-outline-info text-start">
                        <i class="fas fa-file-invoice me-2"></i>
                        Journey Voucher
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Automatic Popup Notifications -->
@if($expiringVehicles->count() > 0)
@php
    $criticalVehicles = $expiringVehicles->filter(function ($vehicle) {
        return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
            return $doc['days_remaining'] <= 3;
        });
    });
    
    $trackerExpiring = $expiringVehicles->filter(function ($vehicle) {
        return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
            return $doc['type'] === 'Tracker' && $doc['days_remaining'] <= 15;
        });
    });
    
    $dipChartExpiring = $expiringVehicles->filter(function ($vehicle) {
        return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
            return $doc['type'] === 'Dip Chart' && $doc['days_remaining'] <= 15;
        });
    });
@endphp

<!-- Critical Alert Modal -->
@if($criticalVehicles->count() > 0)
<div class="modal fade" id="criticalAlertModal" tabindex="-1" aria-labelledby="criticalAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="criticalAlertModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    URGENT: Critical Document Expiry Alert!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Immediate Action Required!</strong>
                    {{ $criticalVehicles->count() }} vehicle(s) have documents expiring within 3 days or already expired.
                </div>
                
                <div class="row">
                    @foreach($criticalVehicles->take(4) as $vehicle)
                    @php
                        $criticalDocs = collect($vehicle->getExpiringDocuments())->filter(fn($doc) => $doc['days_remaining'] <= 3);
                    @endphp
                    <div class="col-md-6 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fas fa-truck me-2"></i>{{ $vehicle->vrn }}
                                </h6>
                                <p class="text-muted small mb-2">Driver: {{ $vehicle->driver_name }}</p>
                                
                                @foreach($criticalDocs as $doc)
                                <div class="alert alert-sm alert-danger py-1 px-2 mb-1">
                                    <i class="fas fa-{{ $doc['type'] == 'Tracker' ? 'map-marker-alt' : ($doc['type'] == 'Dip Chart' ? 'chart-bar' : 'file-alt') }} me-1"></i>
                                    <strong>{{ $doc['type'] }}:</strong>
                                    @if($doc['days_remaining'] <= 0)
                                        <span class="badge bg-white text-danger">EXPIRED</span>
                                    @elseif($doc['days_remaining'] == 1)
                                        <span class="badge bg-white text-danger">TOMORROW</span>
                                    @else
                                        <span class="badge bg-white text-danger">{{ $doc['days_remaining'] }} DAYS</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($criticalVehicles->count() > 4)
                <p class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    And {{ $criticalVehicles->count() - 4 }} more vehicle(s) with critical expiries.
                </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="snoozeNotifications(4)">
                    <i class="fas fa-clock me-1"></i>
                    Remind in 4 Hours
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="snoozeNotifications(24)">
                    <i class="fas fa-clock me-1"></i>
                    Remind Tomorrow
                </button>
                <a href="{{ route('notifications') }}" class="btn btn-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    View All Alerts
                </a>
                <a href="{{ route('vehicles.index', ['filter' => 'expiring']) }}" class="btn btn-outline-danger">
                    <i class="fas fa-truck me-1"></i>
                    Manage Vehicles
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tracker Expiry Alert Modal -->
@if($trackerExpiring->count() > 0)
<div class="modal fade" id="trackerAlertModal" tabindex="-1" aria-labelledby="trackerAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="trackerAlertModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Tracker Expiry Alert
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>GPS Tracker Alert!</strong>
                    {{ $trackerExpiring->count() }} vehicle(s) have GPS trackers expiring within 15 days.
                </div>
                
                <div class="list-group">
                    @foreach($trackerExpiring->take(5) as $vehicle)
                    @php
                        $trackerDoc = collect($vehicle->getExpiringDocuments())->first(fn($doc) => $doc['type'] === 'Tracker');
                    @endphp
                    @if($trackerDoc)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $vehicle->vrn }}</strong> - {{ $vehicle->driver_name }}
                            <br>
                            <small class="text-muted">Expires: {{ $trackerDoc['expiry_date']->format('M d, Y') }}</small>
                        </div>
                        <span class="badge bg-warning">
                            @if($trackerDoc['days_remaining'] <= 0)
                                EXPIRED
                            @elseif($trackerDoc['days_remaining'] == 1)
                                Tomorrow
                            @else
                                {{ $trackerDoc['days_remaining'] }} days
                            @endif
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('vehicles.index', ['document_type' => 'tracker']) }}" class="btn btn-warning">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    View Tracker Expiries
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dip Chart Expiry Alert Modal -->
@if($dipChartExpiring->count() > 0)
<div class="modal fade" id="dipChartAlertModal" tabindex="-1" aria-labelledby="dipChartAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="dipChartAlertModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>
                    Dip Chart Expiry Alert
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Dip Chart Alert!</strong>
                    {{ $dipChartExpiring->count() }} vehicle(s) have dip charts expiring within 15 days.
                </div>
                
                <div class="list-group">
                    @foreach($dipChartExpiring->take(5) as $vehicle)
                    @php
                        $dipChartDoc = collect($vehicle->getExpiringDocuments())->first(fn($doc) => $doc['type'] === 'Dip Chart');
                    @endphp
                    @if($dipChartDoc)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $vehicle->vrn }}</strong> - {{ $vehicle->driver_name }}
                            <br>
                            <small class="text-muted">Expires: {{ $dipChartDoc['expiry_date']->format('M d, Y') }}</small>
                        </div>
                        <span class="badge bg-info">
                            @if($dipChartDoc['days_remaining'] <= 0)
                                EXPIRED
                            @elseif($dipChartDoc['days_remaining'] == 1)
                                Tomorrow
                            @else
                                {{ $dipChartDoc['days_remaining'] }} days
                            @endif
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('vehicles.index', ['document_type' => 'dip_chart']) }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-1"></i>
                    View Dip Chart Expiries
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Notification Settings Modal -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" aria-labelledby="notificationSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationSettingsModalLabel">
                    <i class="fas fa-bell me-2"></i>
                    Notification Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="notificationSettingsForm">
                    <div class="mb-3">
                        <h6>Popup Notifications</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="popupEnabled" name="popup_enabled" checked>
                            <label class="form-check-label" for="popupEnabled">
                                Show popup alerts for expiring documents
                            </label>
                        </div>
                        <small class="text-muted">Popup alerts appear when you visit the dashboard for critical expiries</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Email Notifications</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailEnabled" name="email_enabled" checked>
                            <label class="form-check-label" for="emailEnabled">
                                Receive email alerts for expiring documents
                            </label>
                        </div>
                        <small class="text-muted">Daily email summaries will be sent for expiring documents</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="snoozeHours" class="form-label">Default Snooze Duration</label>
                        <select class="form-select" id="snoozeHours" name="snooze_hours">
                            <option value="4">4 Hours</option>
                            <option value="8">8 Hours</option>
                            <option value="24" selected>24 Hours (1 Day)</option>
                            <option value="48">48 Hours (2 Days)</option>
                            <option value="168">168 Hours (1 Week)</option>
                        </select>
                        <small class="text-muted">How long to snooze notifications when using "Remind Later"</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Current Status</h6>
                        <div id="notificationStatus">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="statusText">Checking notification status...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="resetAllNotificationSettings()">
                    <i class="fas fa-redo me-1"></i>
                    Reset All
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNotificationSettings()">
                    <i class="fas fa-save me-1"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if we should show popup notifications
    const showPopups = localStorage.getItem('dashboard_popup_shown_today') !== '{{ date("Y-m-d") }}' && !isNotificationsSnoozed();
    
    @if($expiringVehicles->count() > 0)
    if (showPopups) {
        // Show critical alerts first (highest priority)
        @if($criticalVehicles->count() > 0)
        setTimeout(function() {
            const criticalModal = new bootstrap.Modal(document.getElementById('criticalAlertModal'));
            criticalModal.show();
            
            // Play alert sound for critical notifications
            playNotificationSound('critical');
            
            // Mark as shown for today
            localStorage.setItem('dashboard_popup_shown_today', '{{ date("Y-m-d") }}');
        }, 1000);
        
        @elseif($trackerExpiring->count() > 0)
        // Show tracker expiry alert if no critical alerts
        setTimeout(function() {
            const trackerModal = new bootstrap.Modal(document.getElementById('trackerAlertModal'));
            trackerModal.show();
            
            playNotificationSound('warning');
            localStorage.setItem('dashboard_popup_shown_today', '{{ date("Y-m-d") }}');
        }, 1000);
        
        @elseif($dipChartExpiring->count() > 0)
        // Show dip chart expiry alert if no critical or tracker alerts
        setTimeout(function() {
            const dipChartModal = new bootstrap.Modal(document.getElementById('dipChartAlertModal'));
            dipChartModal.show();
            
            playNotificationSound('warning');
            localStorage.setItem('dashboard_popup_shown_today', '{{ date("Y-m-d") }}');
        }, 1000);
        @endif
    }
    @endif
    
    // Add manual trigger buttons for admins
    addNotificationControls();
});

// Play notification sound
function playNotificationSound(type = 'warning') {
    // Create audio context for notification sounds
    if (typeof(Audio) !== "undefined") {
        let frequency = type === 'critical' ? 800 : 600;
        let audioContext = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = audioContext.createOscillator();
        let gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = frequency;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    }
}

// Add keyboard shortcuts for notification management
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Shift + N to show notification settings
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'N') {
        e.preventDefault();
        const settingsModal = new bootstrap.Modal(document.getElementById('notificationSettingsModal'));
        settingsModal.show();
    }
    
    // Escape key to dismiss all modals
    if (e.key === 'Escape') {
        // Close any open notification modals
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });
    }
});

// Add mobile-specific touch handlers for better mobile experience
if ('ontouchstart' in window) {
    // Add swipe to dismiss functionality for mobile
    let startY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        const endY = e.changedTouches[0].clientY;
        const modal = e.target.closest('.modal');
        
        if (modal && (startY - endY > 100)) { // Swipe up to dismiss
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        }
    });
}

// Add notification control buttons for testing/manual trigger
function addNotificationControls() {
    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
    // Add a small control panel for admins
    const controlPanel = document.createElement('div');
    controlPanel.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 1000; background: rgba(0,0,0,0.8); padding: 10px; border-radius: 5px;';
    controlPanel.innerHTML = `
        <small style="color: white; display: block; margin-bottom: 5px;">Admin Controls:</small>
        <button class="btn btn-sm btn-outline-light me-1" onclick="resetDashboardPopups()">
            <i class="fas fa-redo"></i> Reset Popups
        </button>
        <button class="btn btn-sm btn-outline-light" onclick="showAllNotifications()">
            <i class="fas fa-bell"></i> Show All
        </button>
    `;
    document.body.appendChild(controlPanel);
    
    // Auto-hide after 10 seconds
    setTimeout(() => {
        controlPanel.style.opacity = '0.3';
    }, 10000);
    @endif
}

// Reset popup shown status (for testing)
function resetDashboardPopups() {
    localStorage.removeItem('dashboard_popup_shown_today');
    location.reload();
}

// Show all notification modals
function showAllNotifications() {
    @if($criticalVehicles->count() > 0)
    const criticalModal = new bootstrap.Modal(document.getElementById('criticalAlertModal'));
    criticalModal.show();
    @endif
    
    setTimeout(() => {
        @if($trackerExpiring->count() > 0)
        const trackerModal = new bootstrap.Modal(document.getElementById('trackerAlertModal'));
        trackerModal.show();
        @endif
    }, 500);
    
    setTimeout(() => {
        @if($dipChartExpiring->count() > 0)
        const dipChartModal = new bootstrap.Modal(document.getElementById('dipChartAlertModal'));
        dipChartModal.show();
        @endif
    }, 1000);
}

// Snooze notifications for specified hours
function snoozeNotifications(hours) {
    const snoozeUntil = new Date();
    snoozeUntil.setHours(snoozeUntil.getHours() + hours);
    localStorage.setItem('dashboard_popup_snoozed_until', snoozeUntil.toISOString());
    
    // Show confirmation
    const toast = document.createElement('div');
    toast.className = 'toast position-fixed bottom-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast-header">
            <i class="fas fa-bell-slash text-muted me-2"></i>
            <strong class="me-auto">Notifications Snoozed</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Popup notifications snoozed for ${hours} hour(s). You'll still see alerts on the dashboard.
        </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}

// Check if notifications are snoozed
function isNotificationsSnoozed() {
    const snoozeUntil = localStorage.getItem('dashboard_popup_snoozed_until');
    if (!snoozeUntil) return false;
    
    const snoozeDate = new Date(snoozeUntil);
    const now = new Date();
    
    if (now < snoozeDate) {
        return true;
    } else {
        // Snooze period expired, remove it
        localStorage.removeItem('dashboard_popup_snoozed_until');
        return false;
    }
}

// Show browser notification if supported
@if($criticalVehicles->count() > 0)
if ("Notification" in window) {
    if (Notification.permission === "granted") {
        showBrowserNotification();
    } else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
                showBrowserNotification();
            }
        });
    }
}

function showBrowserNotification() {
    const notification = new Notification("Transport Fleet Alert", {
        body: "{{ $criticalVehicles->count() }} vehicle(s) have critical document expiries requiring immediate attention!",
        icon: "/favicon.ico",
        badge: "/favicon.ico",
        tag: "fleet-critical-alert",
        requireInteraction: true
    });
    
    notification.onclick = function() {
        window.focus();
        notification.close();
        // Show the critical modal
        const criticalModal = new bootstrap.Modal(document.getElementById('criticalAlertModal'));
        criticalModal.show();
    };
    
    // Auto close after 10 seconds
    setTimeout(() => notification.close(), 10000);
}
@endif

// Load notification settings when modal opens
document.getElementById('notificationSettingsModal').addEventListener('show.bs.modal', function() {
    loadNotificationSettings();
    updateNotificationStatus();
});

// Load current notification settings
function loadNotificationSettings() {
    const settings = JSON.parse(localStorage.getItem('notification_settings') || '{}');
    
    document.getElementById('popupEnabled').checked = settings.popup_enabled !== false;
    document.getElementById('emailEnabled').checked = settings.email_enabled !== false;
    document.getElementById('snoozeHours').value = settings.snooze_hours || 24;
}

// Update notification status display
function updateNotificationStatus() {
    const statusElement = document.getElementById('statusText');
    const alertElement = document.getElementById('notificationStatus').querySelector('.alert');
    
    if (isNotificationsSnoozed()) {
        const snoozeUntil = new Date(localStorage.getItem('dashboard_popup_snoozed_until'));
        const hoursLeft = Math.ceil((snoozeUntil - new Date()) / (1000 * 60 * 60));
        
        statusElement.textContent = `Notifications snoozed for ${hoursLeft} more hour(s)`;
        alertElement.className = 'alert alert-warning';
    } else {
        const shownToday = localStorage.getItem('dashboard_popup_shown_today') === '{{ date("Y-m-d") }}';
        
        if (shownToday) {
            statusElement.textContent = 'Popup shown today - next popup tomorrow';
            alertElement.className = 'alert alert-success';
        } else {
            statusElement.textContent = 'Notifications active - popup will show for new alerts';
            alertElement.className = 'alert alert-info';
        }
    }
}

// Save notification settings
function saveNotificationSettings() {
    const form = document.getElementById('notificationSettingsForm');
    const formData = new FormData(form);
    
    const settings = {
        popup_enabled: formData.get('popup_enabled') === 'on',
        email_enabled: formData.get('email_enabled') === 'on',
        snooze_hours: parseInt(formData.get('snooze_hours'))
    };
    
    // Save to localStorage
    localStorage.setItem('notification_settings', JSON.stringify(settings));
    
    // Send to server (optional - for future database storage)
    fetch('{{ route("notifications.settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast position-fixed bottom-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check text-white me-2"></i>
                    <strong class="me-auto">Settings Saved</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    Your notification preferences have been updated successfully.
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('notificationSettingsModal'));
            modal.hide();
            
            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                document.body.removeChild(toast);
            });
        }
    })
    .catch(error => {
        console.error('Error saving settings:', error);
        alert('Failed to save settings. Please try again.');
    });
}

// Reset all notification settings
function resetAllNotificationSettings() {
    if (confirm('This will reset all notification settings and clear any snooze periods. Continue?')) {
        localStorage.removeItem('notification_settings');
        localStorage.removeItem('dashboard_popup_shown_today');
        localStorage.removeItem('dashboard_popup_snoozed_until');
        
        // Reset form
        document.getElementById('popupEnabled').checked = true;
        document.getElementById('emailEnabled').checked = true;
        document.getElementById('snoozeHours').value = 24;
        
        updateNotificationStatus();
        
        alert('All notification settings have been reset.');
    }
}
</script>
@endpush
