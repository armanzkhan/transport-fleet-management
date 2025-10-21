@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Debit Account Notifications
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
                    @if($debitAccounts->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> The following accounts have debit balances that require attention.
                    </div>

                    <!-- Debit Accounts Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Debit Balance</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debitAccounts as $account)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $account->account_code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $account->account_name }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger fw-bold">₨{{ number_format($account->balance, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">Debit Balance</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Debit Accounts</h5>
                                    <h3>{{ $debitAccounts->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Debit Amount</h5>
                                    <h3>₨{{ number_format($debitAccounts->sum('balance'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Good News:</strong> No accounts have debit balances. All accounts are properly balanced.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
