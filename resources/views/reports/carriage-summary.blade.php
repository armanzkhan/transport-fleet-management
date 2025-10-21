@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-truck me-2"></i>
                            Carriage Summary
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

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Carriages</h5>
                                    <h3>{{ $summary->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Trips</h5>
                                    <h3>{{ $summary->sum('trip_count') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Freight</h5>
                                    <h3>₨{{ number_format($summary->sum('total_freight'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Net Amount</h5>
                                    <h3>₨{{ number_format($summary->sum('net_amount'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carriage Summary Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Carriage Name</th>
                                    <th class="text-center">Trips</th>
                                    <th class="text-end">Total Freight</th>
                                    <th class="text-end">Total Shortage</th>
                                    <th class="text-end">Total Commission</th>
                                    <th class="text-end">Net Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary as $carriage)
                                <tr>
                                    <td>
                                        <strong>{{ $carriage->carriage_name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $carriage->trip_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success">₨{{ number_format($carriage->total_freight, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger">₨{{ number_format($carriage->total_shortage, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-info">₨{{ number_format($carriage->total_commission, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">₨{{ number_format($carriage->net_amount, 2) }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No carriage data found for the selected period.</p>
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
