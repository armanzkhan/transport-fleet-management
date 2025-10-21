@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Monthly Vehicle Bills
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
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="vrn" class="form-label">VRN</label>
                                <input type="text" class="form-control" id="vrn" name="vrn" 
                                       value="{{ request('vrn') }}" placeholder="Enter VRN">
                            </div>
                            <div class="col-md-3">
                                <label for="owner_name" class="form-label">Owner Name</label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                       value="{{ request('owner_name') }}" placeholder="Enter owner name">
                            </div>
                            <div class="col-md-3">
                                <label for="billing_month" class="form-label">Billing Month</label>
                                <input type="month" class="form-control" id="billing_month" name="billing_month" 
                                       value="{{ request('billing_month') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('reports.monthly-vehicle-bills') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bills Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>VRN</th>
                                    <th>Owner</th>
                                    <th>Billing Month</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                <tr>
                                    <td>
                                        <strong>{{ $bill->vehicle->vrn ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        {{ $bill->vehicle->owner->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $bill->billing_month }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>₨{{ number_format($bill->total_amount, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($bill->is_finalized)
                                            <span class="badge bg-success">Finalized</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('vehicle-billing.show', $bill) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$bill->is_finalized)
                                            <a href="{{ route('vehicle-billing.edit', $bill) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No vehicle bills found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($bills->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bills->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
