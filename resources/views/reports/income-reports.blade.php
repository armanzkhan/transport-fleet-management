@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Income Reports
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

                    <!-- Income Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Freight Difference Income</h5>
                                    <h3>₨{{ number_format($freightDifference->total_income ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Shortage Difference Income</h5>
                                    <h3>₨{{ number_format($shortageDifference->total_income ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Commission Income</h5>
                                    <h3>₨{{ number_format($companyIncome->sum('commission_income'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Income</h5>
                                    <h3>₨{{ number_format(($freightDifference->total_income ?? 0) + ($shortageDifference->total_income ?? 0) + $companyIncome->sum('commission_income'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company-wise Income Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Company</th>
                                    <th class="text-end">Commission Income</th>
                                    <th class="text-end">Freight Difference</th>
                                    <th class="text-end">Shortage Difference</th>
                                    <th class="text-end">Total Income</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companyIncome as $company)
                                <tr>
                                    <td>
                                        <strong>{{ $company->company }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success">₨{{ number_format($company->commission_income, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-info">₨{{ number_format($company->freight_difference, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-warning">₨{{ number_format($company->shortage_difference, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">₨{{ number_format($company->commission_income + $company->freight_difference + $company->shortage_difference, 2) }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No income data found for the selected period.</p>
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
@endsection
