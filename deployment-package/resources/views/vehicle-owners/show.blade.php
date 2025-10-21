@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">{{ $vehicleOwner->name }}</h2>
                <p class="text-muted mb-0">Vehicle Owner Details</p>
            </div>
            <div>
                @can('edit-vehicle-owners')
                <a href="{{ route('vehicle-owners.edit', $vehicleOwner) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('vehicle-owners.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Owner Information -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>
                    Owner Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Serial Number</label>
                        <div>
                            <span class="badge bg-secondary fs-6">{{ $vehicleOwner->serial_number }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">CNIC Number</label>
                        <div class="text-muted">{{ $vehicleOwner->cnic }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Contact Number</label>
                        <div class="text-muted">{{ $vehicleOwner->contact_number }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <div>
                            @if($vehicleOwner->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($vehicleOwner->address)
                    <div class="col-12 mb-3">
                        <label class="form-label fw-medium">Address</label>
                        <div class="text-muted">{{ $vehicleOwner->address }}</div>
                    </div>
                    @endif
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Created At</label>
                        <div class="text-muted">{{ $vehicleOwner->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Last Updated</label>
                        <div class="text-muted">{{ $vehicleOwner->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner Statistics -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Vehicles</div>
                        <div class="h4 mb-0">{{ $vehicleOwner->vehicles->count() }}</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-route fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Active Vehicles</div>
                        <div class="h4 mb-0">{{ $vehicleOwner->vehicles->where('is_active', true)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner's Vehicles -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-truck me-2"></i>
                    Owner's Vehicles
                </h5>
            </div>
            <div class="card-body p-0">
                @if($vehicleOwner->vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>VRN</th>
                                <th>Driver</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicleOwner->vehicles as $vehicle)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $vehicle->vrn }}</span>
                                </td>
                                <td>{{ $vehicle->driver_name }}</td>
                                <td>{{ number_format($vehicle->capacity) }} L</td>
                                <td>
                                    @if($vehicle->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('view-vehicles')
                                    <a href="{{ route('vehicles.show', $vehicle) }}" 
                                       class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <div>No vehicles registered under this owner.</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
