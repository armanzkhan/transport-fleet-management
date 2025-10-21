@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Unattached Shortages
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
                    @if($shortages->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> The following journey vouchers have shortages that are not yet billed.
                    </div>

                    <!-- Shortages Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Journey Date</th>
                                    <th>VRN</th>
                                    <th>Owner</th>
                                    <th>Route</th>
                                    <th class="text-end">Shortage Amount</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shortages as $shortage)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $shortage->journey_date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $shortage->vehicle->vrn ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        {{ $shortage->vehicle->owner->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $shortage->loading_point }} → {{ $shortage->destination }}
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger fw-bold">₨{{ number_format($shortage->shortage_amount, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">Unbilled</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Unattached Shortages</h5>
                                    <h3>{{ $shortages->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Shortage Amount</h5>
                                    <h3>₨{{ number_format($shortages->sum('shortage_amount'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($shortages->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $shortages->links() }}
                    </div>
                    @endif
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Good News:</strong> No unattached shortages found. All journey vouchers are properly billed.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
