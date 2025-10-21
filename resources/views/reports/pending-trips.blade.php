@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Pending Trips Report</h2>
                <p class="text-muted mb-0">Journey vouchers awaiting processing and billing.</p>
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

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h4>{{ $unprocessedTrips->total() }}</h4>
                <p class="mb-0">Unprocessed Trips</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-file-invoice fa-2x mb-2"></i>
                <h4>{{ $readyForBilling->total() }}</h4>
                <p class="mb-0">Ready for Billing</p>
            </div>
        </div>
    </div>
</div>

<!-- Unprocessed Trips -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Unprocessed Trips ({{ $unprocessedTrips->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($unprocessedTrips->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Journey #</th>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Company</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unprocessedTrips as $trip)
                            <tr>
                                <td>
                                    <a href="{{ route('journey-vouchers.show', $trip) }}" class="text-decoration-none">
                                        {{ $trip->journey_number }}
                                    </a>
                                </td>
                                <td>{{ $trip->journey_date->format('d M Y') }}</td>
                                <td>
                                    {{ $trip->vehicle->vrn }}
                                    <br><small class="text-muted">{{ $trip->vehicle->driver_name }}</small>
                                </td>
                                <td>
                                    {{ $trip->loading_point }} → {{ $trip->destination }}
                                    <br><small class="text-muted">{{ $trip->product }}</small>
                                </td>
                                <td>{{ $trip->company }}</td>
                                <td>
                                    <strong>₨{{ number_format($trip->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Unprocessed</span>
                                </td>
                                <td>
                                    <a href="{{ route('journey-vouchers.show', $trip) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $unprocessedTrips->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h6 class="text-success">All trips are processed!</h6>
                    <p class="text-muted mb-0">No unprocessed trips found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ready for Billing -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Ready for Billing ({{ $readyForBilling->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($readyForBilling->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Journey #</th>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Company</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($readyForBilling as $trip)
                            <tr>
                                <td>
                                    <a href="{{ route('journey-vouchers.show', $trip) }}" class="text-decoration-none">
                                        {{ $trip->journey_number }}
                                    </a>
                                </td>
                                <td>{{ $trip->journey_date->format('d M Y') }}</td>
                                <td>
                                    {{ $trip->vehicle->vrn }}
                                    <br><small class="text-muted">{{ $trip->vehicle->driver_name }}</small>
                                </td>
                                <td>
                                    {{ $trip->loading_point }} → {{ $trip->destination }}
                                    <br><small class="text-muted">{{ $trip->product }}</small>
                                </td>
                                <td>{{ $trip->company }}</td>
                                <td>
                                    <strong>₨{{ number_format($trip->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">Ready for Billing</span>
                                </td>
                                <td>
                                    <a href="{{ route('journey-vouchers.show', $trip) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $readyForBilling->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                    <h6 class="text-info">No trips ready for billing</h6>
                    <p class="text-muted mb-0">All processed trips have been billed.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
