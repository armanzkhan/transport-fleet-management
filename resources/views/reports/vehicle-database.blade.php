@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-database me-2"></i>
                            Vehicle Database
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Reports
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="vrn" class="form-label">VRN</label>
                                <input type="text" class="form-control" id="vrn" name="vrn" 
                                       value="{{ request('vrn') }}" placeholder="Enter VRN">
                            </div>
                            <div class="col-md-4">
                                <label for="owner_name" class="form-label">Owner Name</label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                       value="{{ request('owner_name') }}" placeholder="Enter owner name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('reports.vehicle-database') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Vehicles</h5>
                                    <h3>{{ $vehicles->total() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Active Vehicles</h5>
                                    <h3>{{ $vehicles->where('is_active', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Inactive Vehicles</h5>
                                    <h3>{{ $vehicles->where('is_active', false)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Unique Owners</h5>
                                    <h3>{{ $vehicles->pluck('owner_id')->unique()->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>VRN</th>
                                    <th>Owner</th>
                                    <th>Make & Model</th>
                                    <th>Capacity</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <strong>{{ $vehicle->vrn }}</strong>
                                    </td>
                                    <td>
                                        {{ $vehicle->owner->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $vehicle->make }} {{ $vehicle->model }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $vehicle->capacity }} tons</span>
                                    </td>
                                    <td class="text-center">
                                        @if($vehicle->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No vehicles found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($vehicles->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $vehicles->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
