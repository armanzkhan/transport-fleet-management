@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Vehicle Owners</h2>
                <p class="text-muted mb-0">Manage vehicle owners and their information.</p>
            </div>
            <div>
                @can('create-vehicle-owners')
                <a href="{{ route('vehicle-owners.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Owner
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('vehicle-owners.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name, CNIC, or contact...">
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
                            <a href="{{ route('vehicle-owners.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Owners Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>S.No</th>
                                <th>Serial Number</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Contact</th>
                                <th>Vehicles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($owners as $index => $owner)
                            <tr>
                                <td>{{ $owners->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $owner->serial_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $owner->name }}</div>
                                    @if($owner->address)
                                    <small class="text-muted">{{ Str::limit($owner->address, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $owner->cnic }}</td>
                                <td>{{ $owner->contact_number }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $owner->vehicles->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-vehicle-owners')
                                        <a href="{{ route('vehicle-owners.show', $owner) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-vehicle-owners')
                                        <a href="{{ route('vehicle-owners.edit', $owner) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-vehicle-owners')
                                        <form method="POST" action="{{ route('vehicle-owners.destroy', $owner) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this owner?')">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <div>No vehicle owners found.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($owners->hasPages())
            <div class="card-footer bg-white">
                {{ $owners->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
