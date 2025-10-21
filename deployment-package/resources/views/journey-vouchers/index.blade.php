@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Journey Vouchers</h2>
                <p class="text-muted mb-0">Manage journey vouchers for primary and secondary loads.</p>
            </div>
            <div>
                @can('create-journey-vouchers')
                <div class="btn-group" role="group">
                    <a href="{{ route('journey-vouchers.primary') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Primary Load
                    </a>
                    <a href="{{ route('journey-vouchers.secondary') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Secondary Load
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('journey-vouchers.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">General Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by VRN, invoice, company...">
                    </div>
                    <div class="col-md-2">
                        <label for="journey_type" class="form-label">Type</label>
                        <select class="form-select" id="journey_type" name="journey_type">
                            <option value="">All Types</option>
                            <option value="primary" {{ request('journey_type') == 'primary' ? 'selected' : '' }}>Primary</option>
                            <option value="secondary" {{ request('journey_type') == 'secondary' ? 'selected' : '' }}>Secondary</option>
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
                        <label for="company_filter" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company_filter" name="search_company" 
                               value="{{ request('search_company') }}" placeholder="Filter by company...">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- Advanced Search Filters -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="accordion" id="advancedSearchAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="advancedSearchHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch">
                                        <i class="fas fa-filter me-2"></i>
                                        Advanced Search Filters
                                    </button>
                                </h2>
                                <div id="advancedSearch" class="accordion-collapse collapse" aria-labelledby="advancedSearchHeader" data-bs-parent="#advancedSearchAccordion">
                                    <div class="accordion-body">
                                        <form method="GET" action="{{ route('journey-vouchers.index') }}" class="row g-3">
                                            <div class="col-md-3">
                                                <label for="search_carriage" class="form-label">Carriage Name</label>
                                                <input type="text" class="form-control" id="search_carriage" name="search_carriage" 
                                                       value="{{ request('search_carriage') }}" placeholder="Search by carriage...">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search_loading_point" class="form-label">Loading Point</label>
                                                <input type="text" class="form-control" id="search_loading_point" name="search_loading_point" 
                                                       value="{{ request('search_loading_point') }}" placeholder="Search by loading point...">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search_destination" class="form-label">Destination</label>
                                                <input type="text" class="form-control" id="search_destination" name="search_destination" 
                                                       value="{{ request('search_destination') }}" placeholder="Search by destination...">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search_vrn" class="form-label">VRN</label>
                                                <input type="text" class="form-control" id="search_vrn" name="search_vrn" 
                                                       value="{{ request('search_vrn') }}" placeholder="Search by VRN...">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search_invoice" class="form-label">Invoice Number</label>
                                                <input type="text" class="form-control" id="search_invoice" name="search_invoice" 
                                                       value="{{ request('search_invoice') }}" placeholder="Search by invoice...">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search_date" class="form-label">Specific Date</label>
                                                <input type="date" class="form-control" id="search_date" name="search_date" 
                                                       value="{{ request('search_date') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search me-1"></i>
                                                        Apply Advanced Filters
                                                    </button>
                                                    <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-times me-1"></i>
                                                        Clear All
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journey Vouchers Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Journey #</th>
                                <th>VRN</th>
                                <th>Route</th>
                                <th>Company</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journeyVouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->journey_date->format('M d, Y') }}</td>
                                <td>
                                    @if($voucher->journey_type == 'primary')
                                        <span class="badge bg-primary">Primary</span>
                                    @else
                                        <span class="badge bg-success">Secondary</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $voucher->journey_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $voucher->vehicle->vrn }}</div>
                                    <small class="text-muted">{{ $voucher->vehicle->owner->name }}</small>
                                </td>
                                <td>
                                    <div>{{ $voucher->loading_point }} → {{ $voucher->destination }}</div>
                                    <small class="text-muted">{{ $voucher->carriage_name }}</small>
                                </td>
                                <td>{{ $voucher->company }}</td>
                                <td>{{ $voucher->product }}</td>
                                <td>
                                    <div class="fw-medium">{{ number_format($voucher->total_amount, 2) }}</div>
                                    @if($voucher->freight_amount > 0)
                                    <small class="text-muted">Freight: {{ number_format($voucher->freight_amount, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->is_direct_bill)
                                        <span class="badge bg-warning">Direct Bill</span>
                                    @elseif($voucher->is_processed)
                                        <span class="badge bg-info">Processed</span>
                                    @elseif($voucher->is_billed)
                                        <span class="badge bg-success">Billed</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-journey-vouchers')
                                        <a href="{{ route('journey-vouchers.show', $voucher) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-journey-vouchers')
                                        <a href="{{ route('journey-vouchers.edit', $voucher) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-journey-vouchers')
                                        <form method="POST" action="{{ route('journey-vouchers.destroy', $voucher) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this voucher?')">
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
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                    <div>No journey vouchers found.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($journeyVouchers->hasPages())
            <div class="card-footer bg-white">
                {{ $journeyVouchers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
