@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-balance-scale me-2"></i>
                            Company Trial Balance
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
                    <!-- Date Range Filter -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ $dateTo ? $dateTo->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
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

                    <!-- Trial Balance Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-danger">Total Debits</h5>
                                    <h3 class="text-danger">₨{{ number_format($totalDebits, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Total Credits</h5>
                                    <h3 class="text-success">₨{{ number_format($totalCredits, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trial Balance Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Debits</th>
                                    <th class="text-end">Credits</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trialBalance as $account)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $account->account_code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $account->account_name }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if($account->total_debits > 0)
                                            <span class="text-danger">₨{{ number_format($account->total_debits, 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($account->total_credits > 0)
                                            <span class="text-success">₨{{ number_format($account->total_credits, 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $balance = $account->total_debits - $account->total_credits;
                                        @endphp
                                        @if($balance > 0)
                                            <span class="text-danger">₨{{ number_format($balance, 2) }}</span>
                                        @elseif($balance < 0)
                                            <span class="text-success">₨{{ number_format(abs($balance), 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No trial balance data found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">TOTAL</th>
                                    <th class="text-end text-danger">₨{{ number_format($totalDebits, 2) }}</th>
                                    <th class="text-end text-success">₨{{ number_format($totalCredits, 2) }}</th>
                                    <th class="text-end">
                                        @php
                                            $netBalance = $totalDebits - $totalCredits;
                                        @endphp
                                        @if($netBalance > 0)
                                            <span class="text-danger">₨{{ number_format($netBalance, 2) }}</span>
                                        @elseif($netBalance < 0)
                                            <span class="text-success">₨{{ number_format(abs($netBalance), 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Balance Check -->
                    @if($totalDebits != $totalCredits)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Trial balance is not balanced. 
                        Difference: ₨{{ number_format(abs($totalDebits - $totalCredits), 2) }}
                    </div>
                    @else
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success:</strong> Trial balance is balanced.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
