@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Cash Book</h2>
                <p class="text-muted mb-0">Manage daily cash transactions and entries.</p>
            </div>
            <div>
                @can('create-cash-book')
                <div class="btn-group" role="group">
                    <a href="{{ route('cash-books.simple') }}" class="btn btn-info">
                        <i class="fas fa-plus-circle me-2"></i>
                        Simple Entry
                    </a>
                    <a href="{{ route('cash-books.daily') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Daily Entry
                    </a>
                    <a href="{{ route('cash-books.receive') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Receive Entry
                    </a>
                    <a href="{{ route('cash-books.payment') }}" class="btn btn-warning">
                        <i class="fas fa-minus me-2"></i>
                        Payment Entry
                    </a>
                </div>
                @endcan
                
                <!-- Export Buttons -->
                <div class="btn-group ms-2" role="group">
                    <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('cash-books.export-csv', request()->query()) }}">
                            <i class="fas fa-file-csv me-2"></i>
                            Export to CSV
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('cash-books.export-html', request()->query()) }}">
                            <i class="fas fa-file-code me-2"></i>
                            Export to HTML
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Summary Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary bg-gradient">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Previous Day Balance</h6>
                                <h4 class="mb-0">₨{{ number_format($previousDayBalance, 2) }}</h4>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-calendar-day fa-2x"></i>
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
                                <h6 class="card-title mb-1 text-white-50">Total Receives</h6>
                                <h4 class="mb-0">₨{{ number_format($totals['total_receives'], 2) }}</h4>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-arrow-up fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-danger bg-gradient">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Total Payments</h6>
                                <h4 class="mb-0">₨{{ number_format($totals['total_payments'], 2) }}</h4>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-arrow-down fa-2x"></i>
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
                                <h6 class="card-title mb-1 text-white-50">Current Cash in Hand</h6>
                                <h4 class="mb-0">₨{{ number_format($todaysCashInHand, 2) }}</h4>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('cash-books.index') }}" id="search-form">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search description, CB number, TRX number...">
                        </div>
                        <div class="col-md-2">
                            <label for="transaction_type" class="form-label">Transaction Type</label>
                            <select class="form-select" id="transaction_type" name="transaction_type">
                                <option value="">All Types</option>
                                <option value="receive" {{ request('transaction_type') == 'receive' ? 'selected' : '' }}>Receive</option>
                                <option value="payment" {{ request('transaction_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <select class="form-select" id="payment_type" name="payment_type">
                                <option value="">All Payment Types</option>
                                <option value="Advance" {{ request('payment_type') == 'Advance' ? 'selected' : '' }}>Advance</option>
                                <option value="Expense" {{ request('payment_type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                                <option value="Shortage" {{ request('payment_type') == 'Shortage' ? 'selected' : '' }}>Shortage</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="vehicle_id" class="form-label">Vehicle</label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vrn }} - {{ $vehicle->owner->name ?? 'N/A' }}
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
                    
                    <div class="row g-3 mt-2">
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
                                <a href="{{ route('cash-books.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Clear Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cash Book Entries Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Transaction #</th>
                                <th>Account</th>
                                <th>Vehicle</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashBooks as $cashBook)
                            <tr>
                                <td>{{ $cashBook->entry_date->format('M d, Y') }}</td>
                                <td>
                                    @if($cashBook->transaction_type == 'receive')
                                        <span class="badge bg-success">Receive</span>
                                    @else
                                        <span class="badge bg-warning">Payment</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $cashBook->transaction_number }}</span>
                                </td>
                                <td>{{ $cashBook->account->account_name }}</td>
                                <td>
                                    @if($cashBook->vehicle)
                                        {{ $cashBook->vehicle->vrn }}
                                        <br><small class="text-muted">{{ $cashBook->vehicle->owner->name }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($cashBook->description, 50) }}</td>
                                <td>
                                    <span class="fw-medium">
                                        {{ number_format($cashBook->amount, 2) }}
                                    </span>
                                </td>
                                <td>{{ $cashBook->creator->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view-cash-book')
                                        <a href="{{ route('cash-books.show', $cashBook) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('print-cash-vouchers')
                                        <a href="{{ route('cash-books.print', $cashBook) }}" 
                                           class="btn btn-outline-info" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-cash-book')
                                        <a href="{{ route('cash-books.edit', $cashBook) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-cash-book')
                                        <form method="POST" action="{{ route('cash-books.destroy', $cashBook) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this entry?')">
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
                                    <i class="fas fa-book fa-3x mb-3"></i>
                                    <div>No cash book entries found.</div>
                                </td>
                            </tr>
                            @endforelse
                            
                            @if($cashBooks->count() > 0)
                            <!-- Total Row -->
                            <tr class="table-secondary border-top border-2">
                                <td colspan="6" class="fw-bold text-end py-3">
                                    <i class="fas fa-calculator me-2"></i>
                                    <strong>CALCULATION SUMMARY:</strong>
                                </td>
                                <td class="fw-bold py-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-success mb-1">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            <strong>Receives: ₨{{ number_format($totals['total_receives'], 2) }}</strong>
                                        </span>
                                        <span class="text-danger mb-1">
                                            <i class="fas fa-minus-circle me-1"></i>
                                            <strong>Payments: ₨{{ number_format($totals['total_payments'], 2) }}</strong>
                                        </span>
                                        <hr class="my-1 border-2">
                                        <span class="fw-bold fs-6 {{ $totals['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-equals me-1"></i>
                                            <strong>NET BALANCE: ₨{{ number_format($totals['net_balance'], 2) }}</strong>
                                        </span>
                                    </div>
                                </td>
                                <td colspan="2" class="text-center py-3">
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-primary mb-1">
                                            {{ $cashBooks->total() }} Total Entries
                                        </span>
                                        @if(request()->hasAny(['search', 'transaction_type', 'payment_type', 'vehicle_id', 'date_from', 'date_to']))
                                        <span class="badge bg-info">
                                            Filtered Results
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($cashBooks->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $cashBooks->firstItem() ?? 0 }} to {{ $cashBooks->lastItem() ?? 0 }} of {{ $cashBooks->total() }} entries
                    </div>
                    <div class="d-flex align-items-center">
                        <!-- Per page selector -->
                        <div class="me-3">
                            <label for="per_page" class="form-label me-2 mb-0">Per page:</label>
                            <select id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        
                        <!-- Custom pagination -->
                        <nav aria-label="Cash books pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Previous Page Link -->
                                @if ($cashBooks->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                            Previous
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $cashBooks->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i>
                                            Previous
                                        </a>
                                    </li>
                                @endif

                                <!-- Pagination Elements -->
                                @php
                                    $currentPage = $cashBooks->currentPage();
                                    $lastPage = $cashBooks->lastPage();
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $currentPage + 2);
                                @endphp
                                
                                @if($startPage > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $cashBooks->url(1) }}">1</a>
                                    </li>
                                    @if($startPage > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif
                                
                                @for($page = $startPage; $page <= $endPage; $page++)
                                    @if ($page == $currentPage)
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $cashBooks->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endfor
                                
                                @if($endPage < $lastPage)
                                    @if($endPage < $lastPage - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $cashBooks->url($lastPage) }}">{{ $lastPage }}</a>
                                    </li>
                                @endif

                                <!-- Next Page Link -->
                                @if ($cashBooks->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $cashBooks->nextPageUrl() }}" rel="next">
                                            Next
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            Next
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pagination .page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    color: #6c757d;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-link i {
    font-size: 0.875rem;
}

.card-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter changes
    const filterSelects = document.querySelectorAll('#transaction_type, #payment_type, #vehicle_id, #per_page, #date_from, #date_to');
    
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    });
    
    // Add search functionality with debouncing
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('search-form').submit();
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Add loading state for search
    const searchForm = document.getElementById('search-form');
    const searchButton = searchForm.querySelector('button[type="submit"]');
    const originalButtonContent = searchButton.innerHTML;
    
    searchForm.addEventListener('submit', function() {
        searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        searchButton.disabled = true;
    });
    
    // Show current filter count
    updateFilterCount();
    
    function updateFilterCount() {
        const activeFilters = [];
        const urlParams = new URLSearchParams(window.location.search);
        
        urlParams.forEach((value, key) => {
            if (key !== 'page' && key !== 'per_page' && value) {
                activeFilters.push(key);
            }
        });
        
        if (activeFilters.length > 0) {
            const filterBadge = document.createElement('span');
            filterBadge.className = 'badge bg-primary ms-2';
            filterBadge.textContent = activeFilters.length + ' filter(s) active';
            
            const searchButton = document.querySelector('#search-form button[type="submit"]');
            if (searchButton && !searchButton.querySelector('.badge')) {
                searchButton.appendChild(filterBadge);
            }
        }
    }
    
    // Per page change handler
    window.changePerPage = function(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    };
});
</script>
@endpush
