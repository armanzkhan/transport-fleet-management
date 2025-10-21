@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Vehicles</h2>
                <p class="text-muted mb-0">Manage vehicles and their information.</p>
            </div>
            <div>
                @can('create-vehicles')
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Vehicle
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('vehicles.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by VRN, driver name, or owner...">
                    </div>
                    <div class="col-md-2">
                        <label for="filter" class="form-label">Filter</label>
                        <select class="form-select" id="filter" name="filter">
                            <option value="">All Vehicles</option>
                            <option value="expiring" {{ request('filter') == 'expiring' ? 'selected' : '' }}>Expiring Documents</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="">All Documents</option>
                            <option value="tracker" {{ request('document_type') == 'tracker' ? 'selected' : '' }}>Tracker Expiry</option>
                            <option value="dip_chart" {{ request('document_type') == 'dip_chart' ? 'selected' : '' }}>Dip Chart</option>
                            <option value="token_tax" {{ request('document_type') == 'token_tax' ? 'selected' : '' }}>Token Tax</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Per Page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>
                                Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2 text-primary"></i>
                        Vehicles List
                    </h5>
                    <small class="text-muted">{{ $vehicles->total() }} total vehicles</small>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">Serial #</th>
                                <th class="border-0 fw-semibold">VRN</th>
                                <th class="border-0 fw-semibold">Owner</th>
                                <th class="border-0 fw-semibold">Driver</th>
                                <th class="border-0 fw-semibold">Capacity</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold">Documents</th>
                                <th class="border-0 fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                            <tr>
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark">{{ $vehicle->serial_number }}</span>
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $vehicle->vrn }}</strong>
                                </td>
                                <td class="align-middle">
                                    {{ $vehicle->owner->name ?? 'N/A' }}
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <strong>{{ $vehicle->driver_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $vehicle->driver_contact }}</small>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ number_format($vehicle->capacity, 0) }} L</span>
                                </td>
                                <td class="align-middle">
                                    @if($vehicle->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($vehicle->hasExpiringDocuments())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Expiring Soon
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">Up to Date</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-vehicles')
                                        <a href="{{ route('vehicles.show', $vehicle) }}" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-vehicles')
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-vehicles')
                                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No vehicles found</h5>
                    <p class="text-muted mb-4">Get started by adding your first vehicle.</p>
                    @can('create-vehicles')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add Vehicle
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            
            @if($vehicles->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $vehicles->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on per_page change
    document.getElementById('per_page').addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endpush