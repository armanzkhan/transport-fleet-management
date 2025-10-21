@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Secondary Journey Voucher</h2>
                <p class="text-muted mb-0">Journey Number: <strong>{{ $secondaryJourneyVoucher->journey_number }}</strong></p>
            </div>
            <div>
                <a href="{{ route('secondary-journey-vouchers.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
                @can('edit-journey-vouchers')
                <a href="{{ route('secondary-journey-vouchers.edit', $secondaryJourneyVoucher) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Voucher Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Voucher Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label text-muted">Journey Number</label>
                        <p class="form-control-plaintext fw-bold">{{ $secondaryJourneyVoucher->journey_number }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Date</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->journey_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Contractor</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->contractor_name }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Company</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->company }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label text-muted">Created By</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->creator->name ?? 'System' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Created At</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Total Entries</label>
                        <p class="form-control-plaintext">{{ $secondaryJourneyVoucher->entries_count }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">PR04 Entries</label>
                        <p class="form-control-plaintext">
                            @if($secondaryJourneyVoucher->pr04_entries_count > 0)
                                <span class="badge bg-warning">{{ $secondaryJourneyVoucher->pr04_entries_count }}</span>
                            @else
                                <span class="badge bg-success">None</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Financial Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Total Freight</h6>
                            <h4 class="text-primary">₨{{ number_format($secondaryJourneyVoucher->total_freight, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Total Shortage</h6>
                            <h4 class="text-danger">₨{{ number_format($secondaryJourneyVoucher->total_shortage, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Company Deduction</h6>
                            <h4 class="text-warning">₨{{ number_format($secondaryJourneyVoucher->total_company_deduction, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Net Amount</h6>
                            <h4 class="text-success">₨{{ number_format($secondaryJourneyVoucher->net_amount, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="text-muted">Vehicle Commission</h6>
                            <h5 class="text-info">₨{{ number_format($secondaryJourneyVoucher->total_vehicle_commission, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="text-muted">Our Commission</h6>
                            <h5 class="text-success">₨{{ number_format($secondaryJourneyVoucher->commission_amount, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Entries Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Journey Entries ({{ $secondaryJourneyVoucher->entries_count }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>S.No</th>
                                <th>VRN</th>
                                <th>Invoice No.</th>
                                <th>Loading Point</th>
                                <th>Destination</th>
                                <th>Product</th>
                                <th>Rate</th>
                                <th>Load Qty</th>
                                <th>Freight</th>
                                <th>Shortage Qty</th>
                                <th>Shortage Amount</th>
                                <th>Company Deduction</th>
                                <th>Vehicle Commission</th>
                                <th>Total</th>
                                <th>PR04</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secondaryJourneyVoucher->entries as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $entry->vrn }}</span>
                                </td>
                                <td>{{ $entry->invoice_number }}</td>
                                <td>{{ $entry->loading_point }}</td>
                                <td>{{ $entry->destination }}</td>
                                <td>{{ $entry->product }}</td>
                                <td>₨{{ number_format($entry->rate, 2) }}</td>
                                <td>{{ number_format($entry->load_quantity, 2) }}</td>
                                <td>
                                    <span class="fw-medium">₨{{ number_format($entry->freight, 2) }}</span>
                                </td>
                                <td>{{ number_format($entry->shortage_quantity, 2) }}</td>
                                <td>₨{{ number_format($entry->shortage_amount, 2) }}</td>
                                <td>₨{{ number_format($entry->company_deduction, 2) }}</td>
                                <td>₨{{ number_format($entry->vehicle_commission, 2) }}</td>
                                <td>
                                    <span class="fw-medium text-success">₨{{ number_format($entry->net_amount, 2) }}</span>
                                </td>
                                <td>
                                    @if($entry->pr04)
                                        <span class="badge bg-warning">PR04</span>
                                    @else
                                        <span class="badge bg-success">Regular</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="{{ route('secondary-journey-vouchers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to List
            </a>
            <div>
                <button type="button" class="btn btn-outline-info me-2" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>
                    Print
                </button>
                @can('edit-journey-vouchers')
                <a href="{{ route('secondary-journey-vouchers.edit', $secondaryJourneyVoucher) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .card-header, .navbar, .sidebar {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    .table {
        font-size: 12px;
    }
}
</style>
@endpush
@endsection
