@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">General Ledger</h2>
                <p class="text-muted mb-0">Complete transaction history and account balances.</p>
            </div>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filters
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.general-ledger') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="account_id" class="form-label">Account</label>
                            <select class="form-select" id="account_id" name="account_id">
                                <option value="">All Accounts</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Transactions
                </h5>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Entry #</th>
                                <th>Account</th>
                                <th>Vehicle</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->entry_date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('cash-books.show', $transaction) }}" class="text-decoration-none">
                                        {{ $transaction->cash_book_number }}
                                    </a>
                                </td>
                                <td>{{ $transaction->account->account_name }}</td>
                                <td>
                                    @if($transaction->vehicle)
                                        {{ $transaction->vehicle->vrn }}
                                        <br><small class="text-muted">{{ $transaction->vehicle->driver_name }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->transaction_type === 'receive' ? 'success' : 'danger' }}">
                                        {{ ucfirst($transaction->transaction_type) }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="{{ $transaction->transaction_type === 'receive' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->transaction_type === 'receive' ? '+' : '-' }}₨{{ number_format($transaction->amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <strong>₨{{ number_format($transaction->total_cash_in_hand, 2) }}</strong>
                                </td>
                                <td>{{ $transaction->creator->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Transactions Found</h5>
                    <p class="text-muted">No transactions match your filter criteria.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
