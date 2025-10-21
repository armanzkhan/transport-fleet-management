@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Vehicle Details</h2>
                <p class="text-muted mb-0">View detailed information about {{ $vehicle->vrn }}</p>
            </div>
            <div class="d-flex gap-2">
                @can('edit-vehicles')
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit Vehicle
                </a>
                @endcan
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Vehicles
                </a>
            </div>
        </div>
    </div>

    <!-- Vehicle Overview -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-truck me-2 text-primary"></i>
                    Vehicle Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Serial Number</label>
                        <div class="fw-semibold">{{ $vehicle->serial_number }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">VRN</label>
                        <div class="fw-semibold fs-5 text-primary">{{ $vehicle->vrn }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Owner</label>
                        <div class="fw-semibold">
                            @if($vehicle->owner)
                                <a href="{{ route('vehicle-owners.show', $vehicle->owner) }}" class="text-decoration-none">
                                    {{ $vehicle->owner->name }}
                                </a>
                            @else
                                <span class="text-muted">No owner assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Capacity</label>
                        <div class="fw-semibold">
                            <span class="badge bg-info fs-6">{{ number_format($vehicle->capacity, 0) }} Liters</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @if($vehicle->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Induction Date</label>
                        <div class="fw-semibold">
                            {{ $vehicle->induction_date ? $vehicle->induction_date->format('M d, Y') : 'Not specified' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Driver Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Driver Name</label>
                        <div class="fw-semibold">{{ $vehicle->driver_name }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Contact Number</label>
                        <div class="fw-semibold">
                            <a href="tel:{{ $vehicle->driver_contact }}" class="text-decoration-none">
                                {{ $vehicle->driver_contact }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2 text-primary"></i>
                    Vehicle Specifications
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Engine Number</label>
                        <div class="fw-semibold">{{ $vehicle->engine_number ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Chassis Number</label>
                        <div class="fw-semibold">{{ $vehicle->chassis_number ?: 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracker Information -->
        @if($vehicle->tracker_name || $vehicle->tracker_link || $vehicle->tracker_id)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                    Tracker Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Tracker Name</label>
                        <div class="fw-semibold">{{ $vehicle->tracker_name ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Tracker ID</label>
                        <div class="fw-semibold">{{ $vehicle->tracker_id ?: 'Not specified' }}</div>
                    </div>
                    @if($vehicle->tracker_link)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Tracker Link</label>
                        <div>
                            <a href="{{ $vehicle->tracker_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Open Tracker
                            </a>
                        </div>
                    </div>
                    @endif
                    @if($vehicle->tracker_expiry)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Tracker Expiry</label>
                        <div class="fw-semibold">
                            {{ $vehicle->tracker_expiry->format('M d, Y') }}
                            @if($vehicle->tracker_expiry <= now()->addDays(15))
                                <span class="badge bg-warning ms-2">Expiring Soon</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Document Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Document Status
                </h5>
            </div>
            <div class="card-body">
                @php
                    $expiringDocs = $vehicle->getExpiringDocuments();
                @endphp

                @if(count($expiringDocs) > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Documents Expiring Soon!</strong>
                    </div>
                    @foreach($expiringDocs as $doc)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $doc['type'] }}</span>
                        <span class="badge bg-warning">
                            {{ $doc['days_remaining'] }} days
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        All documents are up to date
                    </div>
                @endif

                <hr>

                <div class="row text-center">
                    <div class="col-12 mb-2">
                        <label class="form-label text-muted">Token Tax</label>
                        <div class="fw-semibold">
                            @if($vehicle->token_tax_expiry)
                                {{ $vehicle->token_tax_expiry->format('M d, Y') }}
                                @if($vehicle->token_tax_expiry <= now()->addDays(15))
                                    <span class="badge bg-warning ms-1">Expiring</span>
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label text-muted">Dip Chart</label>
                        <div class="fw-semibold">
                            @if($vehicle->dip_chart_expiry)
                                {{ $vehicle->dip_chart_expiry->format('M d, Y') }}
                                @if($vehicle->dip_chart_expiry <= now()->addDays(15))
                                    <span class="badge bg-warning ms-1">Expiring</span>
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="h4 mb-0 text-primary">{{ $vehicle->journeyVouchers()->count() }}</div>
                            <small class="text-muted">Total Journeys</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="h4 mb-0 text-success">{{ $vehicle->vehicleBills()->count() }}</div>
                        <small class="text-muted">Total Bills</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @php
                    $recentJourneys = $vehicle->journeyVouchers()->latest()->limit(3)->get();
                @endphp

                @if($recentJourneys->count() > 0)
                    @foreach($recentJourneys as $journey)
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <i class="fas fa-route text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Journey #{{ $journey->id }}</div>
                            <small class="text-muted">{{ $journey->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection