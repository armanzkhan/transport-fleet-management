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
@endsection
