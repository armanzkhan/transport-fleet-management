@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Vehicle Owner Ledger
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
                    <!-- Owner Selection Form -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="owner_id" class="form-label">Select Owner</label>
                                <select class="form-select" id="owner_id" name="owner_id">
                                    <option value="">Select an owner</option>
                                    @foreach($owners as $ownerOption)
                                    <option value="{{ $ownerOption->id }}" {{ $ownerId == $ownerOption->id ? 'selected' : '' }}>
                                        {{ $ownerOption->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ $dateTo ? $dateTo->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($owner)
                    <!-- Owner Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>
                                Owner Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Owner Details</h6>
                                    <p><strong>Name:</strong> {{ $owner->name }}</p>
                                    <p><strong>Email:</strong> {{ $owner->email ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $owner->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Vehicle Information</h6>
                                    <p><strong>Total Vehicles:</strong> {{ $owner->vehicles->count() }}</p>
                                    <p><strong>Active Vehicles:</strong> {{ $owner->vehicles->where('is_active', true)->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>VRN</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th class="text-center">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $transaction['date']->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $transaction['vrn'] }}</strong>
                                    </td>
                                    <td>
                                        {{ $transaction['description'] }}
                                    </td>
                                    <td class="text-end">
                                        @if($transaction['debit'] > 0)
                                            <span class="text-danger">₨{{ number_format($transaction['debit'], 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($transaction['credit'] > 0)
                                            <span class="text-success">₨{{ number_format($transaction['credit'], 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($transaction['type'] == 'journey')
                                            <span class="badge bg-primary">Journey</span>
                                        @else
                                            <span class="badge bg-info">Cash</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No transactions found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    @if($transactions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Debits</h5>
                                    <h3>₨{{ number_format($transactions->sum('debit'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Credits</h5>
                                    <h3>₨{{ number_format($transactions->sum('credit'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Net Balance</h5>
                                    <h3>₨{{ number_format($transactions->sum('credit') - $transactions->sum('debit'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Select an owner</strong> to view their transaction ledger.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
