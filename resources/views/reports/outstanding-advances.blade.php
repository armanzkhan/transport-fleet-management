@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Outstanding Advances
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
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Outstanding Advances</h5>
                                    <h3>{{ $advances->count() }}</h3>
                                    <p class="mb-0">₨{{ number_format($advances->sum('amount'), 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Outstanding Expenses</h5>
                                    <h3>{{ $expenses->count() }}</h3>
                                    <p class="mb-0">₨{{ number_format($expenses->sum('amount'), 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Advances Table -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-hand-holding-usd me-2"></i>
                                Outstanding Advances
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Entry Date</th>
                                            <th>VRN</th>
                                            <th>Owner</th>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($advances as $advance)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $advance->entry_date->format('M d, Y') }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $advance->vehicle->vrn ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                {{ $advance->vehicle->owner->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $advance->description }}
                                            </td>
                                            <td class="text-end">
                                                <span class="text-warning fw-bold">₨{{ number_format($advance->amount, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">Outstanding</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                                    <p>No outstanding advances found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Expenses Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-receipt me-2"></i>
                                Outstanding Expenses
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Entry Date</th>
                                            <th>VRN</th>
                                            <th>Owner</th>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($expenses as $expense)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $expense->entry_date->format('M d, Y') }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $expense->vehicle->vrn ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                {{ $expense->vehicle->owner->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $expense->description }}
                                            </td>
                                            <td class="text-end">
                                                <span class="text-danger fw-bold">₨{{ number_format($expense->amount, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">Outstanding</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                                    <p>No outstanding expenses found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
