@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Cash Book Entry</h2>
                <p class="text-muted mb-0">Transaction #{{ $cashBook->transaction_number }}</p>
            </div>
            <div>
                @can('print-cash-vouchers')
                <a href="{{ route('cash-books.print', $cashBook) }}" class="btn btn-info me-2" target="_blank">
                    <i class="fas fa-print me-2"></i>
                    Print Voucher
                </a>
                @endcan
                @can('edit-cash-book')
                <a href="{{ route('cash-books.edit', $cashBook) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('cash-books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Transaction Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Transaction Number</label>
                        <div>
                            <span class="badge bg-secondary fs-6">{{ $cashBook->transaction_number }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Entry Date</label>
                        <div class="text-muted">{{ $cashBook->entry_date->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Transaction Type</label>
                        <div>
                            @if($cashBook->transaction_type == 'receive')
                                <span class="badge bg-success fs-6">Receive</span>
                            @else
                                <span class="badge bg-warning fs-6">Payment</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($cashBook->payment_type)
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Payment Type</label>
                        <div>
                            <span class="badge bg-info">{{ $cashBook->payment_type }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Account</label>
                        <div class="text-muted">{{ $cashBook->account->account_name }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Vehicle</label>
                        <div class="text-muted">
                            @if($cashBook->vehicle)
                                {{ $cashBook->vehicle->vrn }}
                                <br><small class="text-muted">{{ $cashBook->vehicle->owner->name }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <div class="text-muted">{{ $cashBook->description }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Amount</label>
                        <div class="h4 text-primary">{{ number_format($cashBook->amount, 2) }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Created By</label>
                        <div class="text-muted">{{ $cashBook->creator->name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Balance Information -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Cash Balance
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-coins fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Previous Day Balance</div>
                        <div class="h5 mb-0">{{ number_format($cashBook->previous_day_balance, 2) }}</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-wallet fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Cash in Hand</div>
                        <div class="h5 mb-0">{{ number_format($cashBook->total_cash_in_hand, 2) }}</div>
                    </div>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <div class="text-muted small">Transaction Impact</div>
                    <div class="h4 {{ $cashBook->transaction_type == 'receive' ? 'text-success' : 'text-danger' }}">
                        {{ $cashBook->transaction_type == 'receive' ? '+' : '-' }}{{ number_format($cashBook->amount, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Information -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Audit Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Created At</label>
                        <div class="text-muted">{{ $cashBook->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Last Updated</label>
                        <div class="text-muted">{{ $cashBook->updated_at->format('M d, Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
