@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Tariff Configuration</h2>
                <p class="text-muted mb-0">Manage freight and shortage rates for different routes and companies.</p>
            </div>
            <div>
                @can('create-tariffs')
                <a href="{{ route('tariffs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Tariff
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('tariffs.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by route, company...">
                    </div>
                    <div class="col-md-2">
                        <label for="carriage_name" class="form-label">Carriage</label>
                        <select class="form-select" id="carriage_name" name="carriage_name">
                            <option value="">All Carriages</option>
                            @foreach(\App\Models\MasterData::where('type', 'carriage')->where('is_active', true)->get() as $carriage)
                            <option value="{{ $carriage->name }}" {{ request('carriage_name') == $carriage->name ? 'selected' : '' }}>
                                {{ $carriage->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="company" class="form-label">Company</label>
                        <select class="form-select" id="company" name="company">
                            <option value="">All Companies</option>
                            @foreach(\App\Models\MasterData::where('type', 'company')->where('is_active', true)->get() as $company)
                            <option value="{{ $company->name }}" {{ request('company') == $company->name ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="future" {{ request('status') == 'future' ? 'selected' : '' }}>Future</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="per_page" class="form-label">Per Page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('tariffs.index') }}" class="btn btn-outline-secondary" title="Clear Search">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Results Info -->
    @if(request()->hasAny(['search', 'carriage_name', 'company', 'date_from', 'date_to', 'status']))
    <div class="col-12 mb-3">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Search Results:</strong> Found {{ $tariffs->total() }} tariff(s) matching your criteria.
            @if(request('search'))
                <br><small>Search term: "{{ request('search') }}"</small>
            @endif
        </div>
    </div>
    @endif

    <!-- Tariffs Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tariff #</th>
                                <th>Date Range</th>
                                <th>Carriage</th>
                                <th>Company</th>
                                <th>Route</th>
                                <th>Company Rate</th>
                                <th>Vehicle Rate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tariffs as $tariff)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $tariff->tariff_number }}</span>
                                </td>
                                <td>
                                    <div>{{ $tariff->from_date->format('M d, Y') }}</div>
                                    <div class="text-muted small">to {{ $tariff->to_date->format('M d, Y') }}</div>
                                </td>
                                <td>{{ $tariff->carriage_name }}</td>
                                <td>{{ $tariff->company }}</td>
                                <td>
                                    <div>{{ $tariff->loading_point }} → {{ $tariff->destination }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ number_format($tariff->company_freight_rate, 2) }}</div>
                                    <small class="text-muted">Shortage: {{ number_format($tariff->company_shortage_rate, 2) }}</small>
                                </td>
                                <td>
                                    @if($tariff->vehicle_freight_rate)
                                        <div class="fw-medium">{{ number_format($tariff->vehicle_freight_rate, 2) }}</div>
                                        <small class="text-muted">Shortage: {{ number_format($tariff->vehicle_shortage_rate ?? 0, 2) }}</small>
                                    @else
                                        <span class="text-muted">Same as Company</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tariff->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-tariffs')
                                        <a href="{{ route('tariffs.show', $tariff) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-tariffs')
                                        <a href="{{ route('tariffs.edit', $tariff) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-tariffs')
                                        <form method="POST" action="{{ route('tariffs.destroy', $tariff) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tariff?')">
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
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-route fa-3x mb-3"></i>
                                    <div>No tariffs found.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($tariffs->hasPages())
            <div class="card-footer bg-white">
                {{ $tariffs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
