@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">
                    <i class="fas fa-bell me-2 text-warning"></i>
                    Notifications & Alerts
                </h2>
                <p class="text-muted mb-0">Document expiry notifications and system alerts</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Critical (≤3 days)</div>
                        <div class="h4 mb-0 text-danger">{{ $criticalNotifications->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Warning (4-7 days)</div>
                        <div class="h4 mb-0 text-warning">{{ $warningNotifications->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow-sm border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Info (8-15 days)</div>
                        <div class="h4 mb-0 text-info">{{ $infoNotifications->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Vehicles</div>
                        <div class="h4 mb-0 text-success">{{ $expiringVehicles->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Type Summary -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Document Type Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">Tracker Expiry</div>
                                <div class="text-muted">{{ $trackerExpiring->count() }} vehicles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-bar fa-2x text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">Dip Chart Expiry</div>
                                <div class="text-muted">{{ $dipChartExpiring->count() }} vehicles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt fa-2x text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">Token Tax Expiry</div>
                                <div class="text-muted">{{ $tokenTaxExpiring->count() }} vehicles</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($expiringVehicles->count() > 0)
    <!-- Critical Notifications -->
    @if($criticalNotifications->count() > 0)
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-header bg-danger bg-opacity-10 border-0">
                <h5 class="mb-0 text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Critical - Immediate Action Required
                </h5>
                <small class="text-muted">Documents expiring within 3 days or already expired</small>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($criticalNotifications as $vehicle)
                    @php
                        $expiringDocs = $vehicle->getExpiringDocuments();
                        $criticalDocs = collect($expiringDocs)->filter(fn($doc) => $doc['days_remaining'] <= 3);
                    @endphp
                    <div class="col-lg-6 mb-3">
                        <div class="card border border-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 text-danger">{{ $vehicle->vrn }}</h6>
                                        <p class="text-muted small mb-1">Owner: {{ $vehicle->owner?->name ?? 'N/A' }}</p>
                                        <p class="text-muted small mb-0">Driver: {{ $vehicle->driver_name }}</p>
                                    </div>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                
                                @foreach($criticalDocs as $doc)
                                <div class="alert alert-danger py-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            <i class="fas fa-{{ $doc['type'] == 'Tracker' ? 'map-marker-alt' : ($doc['type'] == 'Dip Chart' ? 'chart-bar' : 'file-alt') }} me-1"></i>
                                            {{ $doc['type'] }}
                                        </span>
                                        <span class="badge bg-white text-danger border">
                                            @if($doc['days_remaining'] <= 0)
                                                <i class="fas fa-times-circle me-1"></i>EXPIRED
                                            @elseif($doc['days_remaining'] == 1)
                                                <i class="fas fa-clock me-1"></i>TOMORROW
                                            @else
                                                <i class="fas fa-hourglass-half me-1"></i>{{ $doc['days_remaining'] }} DAYS
                                            @endif
                                        </span>
                                    </div>
                                    <small class="text-muted">Expires: {{ $doc['expiry_date']->format('M d, Y') }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Warning Notifications -->
    @if($warningNotifications->count() > 0)
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-header bg-warning bg-opacity-10 border-0">
                <h5 class="mb-0 text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Warning - Action Required Soon
                </h5>
                <small class="text-muted">Documents expiring within 4-7 days</small>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($warningNotifications as $vehicle)
                    @php
                        $expiringDocs = $vehicle->getExpiringDocuments();
                        $warningDocs = collect($expiringDocs)->filter(fn($doc) => $doc['days_remaining'] > 3 && $doc['days_remaining'] <= 7);
                    @endphp
                    <div class="col-lg-6 col-xl-4 mb-3">
                        <div class="card border border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 text-primary">{{ $vehicle->vrn }}</h6>
                                        <p class="text-muted small mb-0">{{ $vehicle->driver_name }}</p>
                                    </div>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                
                                @foreach($warningDocs as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-{{ $doc['type'] == 'Tracker' ? 'map-marker-alt' : ($doc['type'] == 'Dip Chart' ? 'chart-bar' : 'file-alt') }} me-1 text-warning"></i>
                                        {{ $doc['type'] }}
                                    </span>
                                    <span class="badge bg-warning">{{ $doc['days_remaining'] }} days</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Info Notifications -->
    @if($infoNotifications->count() > 0)
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-start border-info border-4">
            <div class="card-header bg-info bg-opacity-10 border-0">
                <h5 class="mb-0 text-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Information - Plan Ahead
                </h5>
                <small class="text-muted">Documents expiring within 8-15 days</small>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($infoNotifications as $vehicle)
                    @php
                        $expiringDocs = $vehicle->getExpiringDocuments();
                        $infoDocs = collect($expiringDocs)->filter(fn($doc) => $doc['days_remaining'] > 7);
                    @endphp
                    <div class="col-lg-4 col-xl-3 mb-3">
                        <div class="card border border-info">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1 text-primary">{{ $vehicle->vrn }}</h6>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                
                                @foreach($infoDocs as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-{{ $doc['type'] == 'Tracker' ? 'map-marker-alt' : ($doc['type'] == 'Dip Chart' ? 'chart-bar' : 'file-alt') }} me-1 text-info"></i>
                                        {{ $doc['type'] }}
                                    </span>
                                    <span class="badge bg-info">{{ $doc['days_remaining'] }} days</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @else
    <!-- No Notifications -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4 class="text-success">All Documents Up to Date!</h4>
                <p class="text-muted">No vehicles have documents expiring within the next 15 days.</p>
                <a href="{{ route('vehicles.index') }}" class="btn btn-primary">
                    <i class="fas fa-truck me-2"></i>
                    View All Vehicles
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh notifications every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);

// Add notification sound for critical alerts
@if($criticalNotifications->count() > 0)
document.addEventListener('DOMContentLoaded', function() {
    // You can add a notification sound here if needed
    console.log('Critical notifications detected: {{ $criticalNotifications->count() }}');
});
@endif
</script>
@endpush