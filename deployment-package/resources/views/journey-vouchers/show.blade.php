@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Journey Voucher Details</h2>
                <p class="text-muted mb-0">Journey Number: {{ $journeyVoucher->journey_number }}</p>
            </div>
            <div>
                <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
                @can('edit-journey-vouchers')
                <a href="{{ route('journey-vouchers.edit', $journeyVoucher) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-{{ $journeyVoucher->journey_type === 'primary' ? 'primary' : 'success' }} text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    {{ ucfirst($journeyVoucher->journey_type) }} Journey Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Journey Number</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->journey_number }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Journey Date</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->journey_date->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle</label>
                        <p class="form-control-plaintext">
                            {{ $journeyVoucher->vehicle->vrn }} - {{ $journeyVoucher->vehicle->driver_name }}
                            <br><small class="text-muted">Owner: {{ $journeyVoucher->vehicle->owner->name }}</small>
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Journey Type</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-{{ $journeyVoucher->journey_type === 'primary' ? 'primary' : 'success' }}">
                                {{ ucfirst($journeyVoucher->journey_type) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Carriage Name</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->carriage_name }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Loading Point</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->loading_point }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Destination</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->destination }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Product</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->product }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->company }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Invoice Number</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->invoice_number ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Capacity</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->capacity }} tons</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Decant Capacity</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->decant_capacity ?: $journeyVoucher->capacity }} tons</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Freight Rate</label>
                        <p class="form-control-plaintext">₨{{ number_format($journeyVoucher->company_freight_rate, 2) }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle Freight Rate</label>
                        <p class="form-control-plaintext">₨{{ number_format($journeyVoucher->vehicle_freight_rate ?: $journeyVoucher->company_freight_rate, 2) }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Shortage Quantity</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->shortage_quantity }} tons</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Shortage Rate</label>
                        <p class="form-control-plaintext">₨{{ number_format($journeyVoucher->shortage_rate, 2) }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Deduction %</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->company_deduction_percentage }}%</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle Deduction %</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->vehicle_deduction_percentage }}%</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Billing Month</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->billing_month }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Direct Bill</label>
                        <p class="form-control-plaintext">
                            @if($journeyVoucher->is_direct_bill)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            @if($journeyVoucher->is_processed)
                                @if($journeyVoucher->is_billed)
                                    <span class="badge bg-success">Billed</span>
                                @else
                                    <span class="badge bg-warning">Processed</span>
                                @endif
                            @else
                                <span class="badge bg-info">Pending</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Created By</label>
                        <p class="form-control-plaintext">{{ $journeyVoucher->creator->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Financial Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Company Freight:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($journeyVoucher->decant_capacity * $journeyVoucher->company_freight_rate, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Vehicle Freight:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($journeyVoucher->freight_amount, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Shortage Amount:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($journeyVoucher->shortage_amount, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Commission:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($journeyVoucher->commission_amount, 2) }}</div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-6"><strong>Total Amount:</strong></div>
                    <div class="col-6 text-end"><strong>₨{{ number_format($journeyVoucher->total_amount, 2) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Journey Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6 text-end">{{ $journeyVoucher->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Updated:</strong></div>
                    <div class="col-6 text-end">{{ $journeyVoucher->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                @if($journeyVoucher->freight_amount > 0)
                <div class="row mb-2">
                    <div class="col-6"><strong>Freight Difference:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($journeyVoucher->getFreightDifferenceIncome(), 2) }}</div>
                </div>
                @endif
            </div>
        </div>

        @can('delete-journey-vouchers')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash me-2"></i>
                    Danger Zone
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Once deleted, this journey voucher cannot be recovered.</p>
                <form action="{{ route('journey-vouchers.destroy', $journeyVoucher) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this journey voucher?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash me-2"></i>
                        Delete Journey Voucher
                    </button>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
