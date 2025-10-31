@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Vehicle Bill Details</h2>
                <p class="text-muted mb-0">Bill Number: {{ $vehicleBill->bill_number }}</p>
            </div>
            <div>
                <a href="{{ route('vehicle-billing.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Bills
                </a>
                @can('print-vehicle-bills')
                <a href="{{ route('vehicle-billing.print', $vehicleBill) }}" class="btn btn-outline-info me-2" target="_blank">
                    <i class="fas fa-print me-2"></i>
                    Print
                </a>
                <a href="{{ route('vehicle-billing.export-word', $vehicleBill) }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-file-word me-2"></i>
                    Export Word
                </a>
                @endcan
                @if(!$vehicleBill->is_finalized)
                @can('finalize-vehicle-billing')
                <form action="{{ route('vehicle-billing.finalize', $vehicleBill) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" 
                            onclick="return confirm('Are you sure you want to finalize this bill? This action cannot be undone.')">
                        <i class="fas fa-check me-2"></i>
                        Finalize Bill
                    </button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Billing Reports Tabs (SRS 1.24 & 1.25) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="bill-tab" data-bs-toggle="tab" data-bs-target="#bill-content" 
                        type="button" role="tab" aria-controls="bill-content" aria-selected="true">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Bill Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-trips-tab" data-bs-toggle="tab" data-bs-target="#pending-trips-content" 
                        type="button" role="tab" aria-controls="pending-trips-content" aria-selected="false">
                    <i class="fas fa-clock me-2"></i>
                    Pending Trips
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="outstanding-tab" data-bs-toggle="tab" data-bs-target="#outstanding-content" 
                        type="button" role="tab" aria-controls="outstanding-content" aria-selected="false">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Outstanding Advances/Expenses
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="billingTabsContent">
            <div class="tab-pane fade show active" id="bill-content" role="tabpanel" aria-labelledby="bill-tab">
                <!-- Bill content moved here -->
            </div>
            <div class="tab-pane fade" id="pending-trips-content" role="tabpanel" aria-labelledby="pending-trips-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Pending and Unprocessed Trips</h5>
                    <a href="{{ route('reports.pending-trips', ['vrn' => $vehicleBill->vehicle->vrn]) }}" 
                       class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        View Full Report
                    </a>
                </div>
                <p class="text-muted">This shows trips that are ready for billing but not yet attached to any bill.</p>
                <div id="pending-trips-data" class="mt-3">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Loading pending trips...</p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="outstanding-content" role="tabpanel" aria-labelledby="outstanding-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Outstanding Advances and Expenses</h5>
                    <a href="{{ route('reports.outstanding-advances', ['vrn' => $vehicleBill->vehicle->vrn]) }}" 
                       class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        View Full Report
                    </a>
                </div>
                <p class="text-muted">This shows advances and expenses that are not yet attached to any bill.</p>
                <div id="outstanding-data" class="mt-3">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Loading outstanding data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Bill Summary -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Bill Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Bill Number</label>
                        <p class="form-control-plaintext">{{ $vehicleBill->bill_number }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Billing Month</label>
                        <p class="form-control-plaintext">{{ $vehicleBill->billing_month }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle</label>
                        <p class="form-control-plaintext">
                            {{ $vehicleBill->vehicle->vrn }} - {{ $vehicleBill->vehicle->driver_name }}
                            <br><small class="text-muted">Owner: {{ $vehicleBill->vehicle->owner->name }}</small>
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            @if($vehicleBill->is_finalized)
                                <span class="badge bg-success">Finalized</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Previous Bill Balance</label>
                        <p class="form-control-plaintext">₨{{ number_format($vehicleBill->previous_bill_balance, 2) }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Created By</label>
                        <p class="form-control-plaintext">{{ $vehicleBill->creator->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Financial Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Freight</label>
                        <p class="form-control-plaintext h5 text-success">₨{{ number_format($vehicleBill->total_freight, 2) }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Advance</label>
                        <p class="form-control-plaintext h5 text-warning">₨{{ number_format($vehicleBill->total_advance, 2) }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Expense</label>
                        <p class="form-control-plaintext h5 text-danger">₨{{ number_format($vehicleBill->total_expense, 2) }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Shortage</label>
                        <p class="form-control-plaintext h5 text-danger">₨{{ number_format($vehicleBill->total_shortage, 2) }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gross Profit</label>
                        <p class="form-control-plaintext h5 {{ $vehicleBill->gross_profit >= 0 ? 'text-success' : 'text-danger' }}">
                            ₨{{ number_format($vehicleBill->gross_profit, 2) }}
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Net Profit</label>
                        <p class="form-control-plaintext h5 {{ $vehicleBill->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                            ₨{{ number_format($vehicleBill->net_profit, 2) }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Vehicle Balance</label>
                        <p class="form-control-plaintext h4 {{ $vehicleBill->total_vehicle_balance >= 0 ? 'text-success' : 'text-danger' }}">
                            ₨{{ number_format($vehicleBill->total_vehicle_balance, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Freight Entries -->
        @if($freightEntries->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-route me-2"></i>
                    Freight Entries ({{ $freightEntries->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Journey #</th>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Company</th>
                                <th>Freight Amount</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($freightEntries as $entry)
                            <tr>
                                <td>
                                    <a href="{{ route('journey-vouchers.show', $entry) }}" class="text-decoration-none">
                                        {{ $entry->journey_number }}
                                    </a>
                                </td>
                                <td>{{ $entry->journey_date->format('d M Y') }}</td>
                                <td>
                                    {{ $entry->loading_point }} → {{ $entry->destination }}
                                    <br><small class="text-muted">{{ $entry->product }}</small>
                                </td>
                                <td>{{ $entry->company }}</td>
                                <td>₨{{ number_format($entry->freight_amount, 2) }}</td>
                                <td><strong>₨{{ number_format($entry->total_amount, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Advance Entries -->
        @if($advanceEntries->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Advance Entries ({{ $advanceEntries->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Entry #</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advanceEntries as $entry)
                            <tr>
                                <td>
                                    <a href="{{ route('cash-books.show', $entry) }}" class="text-decoration-none">
                                        {{ $entry->cash_book_number }}
                                    </a>
                                </td>
                                <td>{{ $entry->entry_date->format('d M Y') }}</td>
                                <td>{{ $entry->description }}</td>
                                <td><strong>₨{{ number_format($entry->amount, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Expense Entries -->
        @if($expenseEntries->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Expense Entries ({{ $expenseEntries->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Entry #</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseEntries as $entry)
                            <tr>
                                <td>
                                    <a href="{{ route('cash-books.show', $entry) }}" class="text-decoration-none">
                                        {{ $entry->cash_book_number }}
                                    </a>
                                </td>
                                <td>{{ $entry->entry_date->format('d M Y') }}</td>
                                <td>{{ $entry->description }}</td>
                                <td><strong>₨{{ number_format($entry->amount, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Bill Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6 text-end">{{ $vehicleBill->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Updated:</strong></div>
                    <div class="col-6 text-end">{{ $vehicleBill->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Freight Entries:</strong></div>
                    <div class="col-6 text-end">{{ $freightEntries->count() }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Advance Entries:</strong></div>
                    <div class="col-6 text-end">{{ $advanceEntries->count() }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Expense Entries:</strong></div>
                    <div class="col-6 text-end">{{ $expenseEntries->count() }}</div>
                </div>
            </div>
        </div>

        @if(!$vehicleBill->is_finalized)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bill Actions
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">This bill is in draft status and can be finalized.</p>
                @can('finalize-vehicle-billing')
                <form action="{{ route('vehicle-billing.finalize', $vehicleBill) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" 
                            onclick="return confirm('Are you sure you want to finalize this bill? This action cannot be undone.')">
                        <i class="fas fa-check me-2"></i>
                        Finalize Bill
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Bill Status
                </h5>
            </div>
            <div class="card-body">
                <p class="text-success mb-0">
                    <i class="fas fa-check me-2"></i>
                    This bill has been finalized and cannot be modified.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Move bill details section into first tab
    const billContentTab = document.getElementById('bill-content');
    const billDetailsSection = document.getElementById('bill-details-section');
    if (billContentTab && billDetailsSection) {
        billContentTab.appendChild(billDetailsSection.cloneNode(true));
        billDetailsSection.style.display = 'none';
    }
    
    // Load pending trips when tab is clicked
    document.getElementById('pending-trips-tab').addEventListener('shown.bs.tab', function() {
        loadPendingTrips();
    });
    
    // Load outstanding advances/expenses when tab is clicked
    document.getElementById('outstanding-tab').addEventListener('shown.bs.tab', function() {
        loadOutstandingData();
    });
    
    function loadPendingTrips() {
        const container = document.getElementById('pending-trips-data');
        const vrn = '{{ $vehicleBill->vehicle->vrn }}';
        
        fetch(`{{ route('reports.pending-trips') }}?vrn=${encodeURIComponent(vrn)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                container.innerHTML = data.html;
            } else {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No pending trips found for this vehicle.
                    </div>
                `;
            }
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Could not load pending trips. <a href="{{ route('reports.pending-trips', ['vrn' => $vehicleBill->vehicle->vrn]) }}" target="_blank">View full report</a>
                </div>
            `;
        });
    }
    
    function loadOutstandingData() {
        const container = document.getElementById('outstanding-data');
        const vrn = '{{ $vehicleBill->vehicle->vrn }}';
        
        fetch(`{{ route('reports.outstanding-advances') }}?vrn=${encodeURIComponent(vrn)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                container.innerHTML = data.html;
            } else {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No outstanding advances or expenses found for this vehicle.
                    </div>
                `;
            }
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Could not load outstanding data. <a href="{{ route('reports.outstanding-advances', ['vrn' => $vehicleBill->vehicle->vrn]) }}" target="_blank">View full report</a>
                </div>
            `;
        });
    }
});
</script>
@endpush
@endsection
