@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Secondary Journey Vouchers</h2>
                <p class="text-muted mb-0">Manage secondary freight transactions and deliveries.</p>
            </div>
            <div>
                @can('create-journey-vouchers')
                <a href="{{ route('secondary-journey-vouchers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Create Secondary JV
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('secondary-journey-vouchers.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Journey No, Invoice, Company...">
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
                            <label for="company" class="form-label">Company</label>
                            <select class="form-select" id="company" name="company">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                                        {{ $company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="contractor_name" class="form-label">Contractor</label>
                            <select class="form-select" id="contractor_name" name="contractor_name">
                                <option value="">All Contractors</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor }}" {{ request('contractor_name') == $contractor ? 'selected' : '' }}>
                                        {{ $contractor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Journey Vouchers Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Journey No</th>
                                <th>Date</th>
                                <th>Contractor</th>
                                <th>Company</th>
                                <th>Entries</th>
                                <th>Total Freight</th>
                                <th>Net Amount</th>
                                <th>PR04</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($secondaryVouchers as $voucher)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $voucher->journey_number }}</span>
                                </td>
                                <td>{{ $voucher->journey_date->format('M d, Y') }}</td>
                                <td>{{ $voucher->contractor_name }}</td>
                                <td>{{ $voucher->company }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $voucher->entries_count }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">₨{{ number_format($voucher->total_freight, 2) }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-success">₨{{ number_format($voucher->net_amount, 2) }}</span>
                                </td>
                                <td>
                                    @if($voucher->pr04_entries_count > 0)
                                        <span class="badge bg-warning">{{ $voucher->pr04_entries_count }} PR04</span>
                                    @else
                                        <span class="badge bg-success">Regular</span>
                                    @endif
                                </td>
                                <td>{{ $voucher->creator->name ?? 'System' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-journey-vouchers')
                                        <a href="{{ route('secondary-journey-vouchers.show', $voucher) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-journey-vouchers')
                                        <a href="{{ route('secondary-journey-vouchers.edit', $voucher) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-journey-vouchers')
                                        <form method="POST" action="{{ route('secondary-journey-vouchers.destroy', $voucher) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this secondary journey voucher?')">
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
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>No Secondary Journey Vouchers Found</h5>
                                        <p>Start by creating your first secondary journey voucher.</p>
                                        @can('create-journey-vouchers')
                                        <a href="{{ route('secondary-journey-vouchers.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Create Secondary JV
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($secondaryVouchers->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $secondaryVouchers->firstItem() ?? 0 }} to {{ $secondaryVouchers->lastItem() ?? 0 }} of {{ $secondaryVouchers->total() }} entries
                    </div>
                    <div>
                        {{ $secondaryVouchers->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Summary Cards -->
@if($secondaryVouchers->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary bg-gradient">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 text-white-50">Total Vouchers</h6>
                        <h4 class="mb-0">{{ $secondaryVouchers->total() }}</h4>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success bg-gradient">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 text-white-50">Total Freight</h6>
                        <h4 class="mb-0">₨{{ number_format($secondaryVouchers->sum('total_freight'), 2) }}</h4>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info bg-gradient">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 text-white-50">Net Amount</h6>
                        <h4 class="mb-0">₨{{ number_format($secondaryVouchers->sum('net_amount'), 2) }}</h4>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning bg-gradient">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 text-white-50">PR04 Entries</h6>
                        <h4 class="mb-0">{{ $secondaryVouchers->sum('pr04_entries_count') }}</h4>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
