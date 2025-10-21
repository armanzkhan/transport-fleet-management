@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Company Summary Report</h2>
                <p class="text-muted mb-0">Performance summary by company for the selected period.</p>
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
                    Date Range
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.company-summary') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
@if(isset($summary) && count($summary) > 0)
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-building fa-2x mb-2"></i>
                <h4>{{ count($summary) }}</h4>
                <p class="mb-0">Companies</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                <h4>₨{{ number_format(collect($summary)->sum('total_billed'), 2) }}</h4>
                <p class="mb-0">Total Billed</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-route fa-2x mb-2"></i>
                <h4>{{ collect($summary)->sum('trip_count') }}</h4>
                <p class="mb-0">Total Trips</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4>₨{{ number_format(collect($summary)->sum('total_shortage'), 2) }}</h4>
                <p class="mb-0">Total Shortage</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Company Summary Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>
                    Company Performance Summary
                </h5>
            </div>
            <div class="card-body">
                @if(isset($summary) && count($summary) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Company</th>
                                <th class="text-center">Trips</th>
                                <th class="text-end">Total Billed</th>
                                <th class="text-end">Total Received</th>
                                <th class="text-end">Total Shortage</th>
                                <th class="text-end">Total Deduction</th>
                                <th class="text-end">Net Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary as $company)
                            <tr>
                                <td>
                                    <strong>{{ $company['company'] }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $company['trip_count'] }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">₨{{ number_format($company['total_billed'], 2) }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="text-primary">₨{{ number_format($company['total_received'], 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-danger">₨{{ number_format($company['total_shortage'], 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-warning">₨{{ number_format($company['total_deduction'], 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $company['total_billed'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₨{{ number_format($company['total_billed'], 2) }}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th><strong>Total</strong></th>
                                <th class="text-center">
                                    <strong>{{ collect($summary)->sum('trip_count') }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong>₨{{ number_format(collect($summary)->sum('total_billed'), 2) }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong>₨{{ number_format(collect($summary)->sum('total_received'), 2) }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong>₨{{ number_format(collect($summary)->sum('total_shortage'), 2) }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong>₨{{ number_format(collect($summary)->sum('total_deduction'), 2) }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong>₨{{ number_format(collect($summary)->sum('total_billed'), 2) }}</strong>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Company Data Found</h5>
                    <p class="text-muted">No journey vouchers found for the selected date range.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Date Range Info -->
@if(isset($dateFrom) && isset($dateTo))
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
        </div>
    </div>
</div>
@endif
@endsection
