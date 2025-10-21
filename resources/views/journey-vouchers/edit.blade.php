@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Edit Journey Voucher</h2>
                <p class="text-muted mb-0">Journey Number: {{ $journeyVoucher->journey_number }}</p>
            </div>
            <div>
                <a href="{{ route('journey-vouchers.show', $journeyVoucher) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-eye me-2"></i>
                    View Details
                </a>
                <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-{{ $journeyVoucher->journey_type === 'primary' ? 'primary' : 'success' }} text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit {{ ucfirst($journeyVoucher->journey_type) }} Journey
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('journey-vouchers.update', $journeyVoucher) }}" method="POST" id="editJourneyForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="journey_date" class="form-label">Journey Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('journey_date') is-invalid @enderror" 
                                   id="journey_date" name="journey_date" value="{{ old('journey_date', $journeyVoucher->journey_date->format('Y-m-d')) }}" required>
                            @error('journey_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_id" class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" 
                                    id="vehicle_id" name="vehicle_id" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" 
                                            data-capacity="{{ $vehicle->capacity }}"
                                            {{ old('vehicle_id', $journeyVoucher->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vrn }} - {{ $vehicle->driver_name }} ({{ $vehicle->owner->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="carriage_name" class="form-label">Carriage Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('carriage_name') is-invalid @enderror" 
                                   id="carriage_name" name="carriage_name" value="{{ old('carriage_name', $journeyVoucher->carriage_name) }}" required>
                            @error('carriage_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="loading_point" class="form-label">Loading Point <span class="text-danger">*</span></label>
                            <select class="form-select @error('loading_point') is-invalid @enderror" 
                                    id="loading_point" name="loading_point" required>
                                <option value="">Select Loading Point</option>
                                @foreach($loadingPoints as $point)
                                    <option value="{{ $point->name }}" {{ old('loading_point', $journeyVoucher->loading_point) == $point->name ? 'selected' : '' }}>
                                        {{ $point->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('loading_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('destination') is-invalid @enderror" 
                                    id="destination" name="destination" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->name }}" {{ old('destination', $journeyVoucher->destination) == $destination->name ? 'selected' : '' }}>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="product" class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select @error('product') is-invalid @enderror" 
                                    id="product" name="product" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->name }}" {{ old('product', $journeyVoucher->product) == $product->name ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                            <select class="form-select @error('company') is-invalid @enderror" 
                                    id="company" name="company" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->name }}" {{ old('company', $journeyVoucher->company) == $company->name ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity (Tons) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity', $journeyVoucher->capacity) }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_freight_rate" class="form-label">Company Freight Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('company_freight_rate') is-invalid @enderror" 
                                   id="company_freight_rate" name="company_freight_rate" value="{{ old('company_freight_rate', $journeyVoucher->company_freight_rate) }}" required>
                            @error('company_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_freight_rate" class="form-label">Vehicle Freight Rate</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_freight_rate') is-invalid @enderror" 
                                   id="vehicle_freight_rate" name="vehicle_freight_rate" value="{{ old('vehicle_freight_rate', $journeyVoucher->vehicle_freight_rate) }}">
                            @error('vehicle_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="shortage_quantity" class="form-label">Shortage Quantity</label>
                            <input type="number" step="0.01" class="form-control @error('shortage_quantity') is-invalid @enderror" 
                                   id="shortage_quantity" name="shortage_quantity" value="{{ old('shortage_quantity', $journeyVoucher->shortage_quantity) }}">
                            @error('shortage_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="shortage_rate" class="form-label">Shortage Rate</label>
                            <input type="number" step="0.01" class="form-control @error('shortage_rate') is-invalid @enderror" 
                                   id="shortage_rate" name="shortage_rate" value="{{ old('shortage_rate', $journeyVoucher->shortage_rate) }}">
                            @error('shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_deduction_percentage" class="form-label">Company Deduction %</label>
                            <input type="number" step="0.01" class="form-control @error('company_deduction_percentage') is-invalid @enderror" 
                                   id="company_deduction_percentage" name="company_deduction_percentage" value="{{ old('company_deduction_percentage', $journeyVoucher->company_deduction_percentage) }}">
                            @error('company_deduction_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_deduction_percentage" class="form-label">Vehicle Deduction %</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_deduction_percentage') is-invalid @enderror" 
                                   id="vehicle_deduction_percentage" name="vehicle_deduction_percentage" value="{{ old('vehicle_deduction_percentage', $journeyVoucher->vehicle_deduction_percentage) }}">
                            @error('vehicle_deduction_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $journeyVoucher->invoice_number) }}">
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="billing_month" class="form-label">Billing Month <span class="text-danger">*</span></label>
                            <input type="month" class="form-control @error('billing_month') is-invalid @enderror" 
                                   id="billing_month" name="billing_month" value="{{ old('billing_month', $journeyVoucher->billing_month) }}" required>
                            @error('billing_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="decant_capacity" class="form-label">Decant Capacity</label>
                            <input type="number" step="0.01" class="form-control @error('decant_capacity') is-invalid @enderror" 
                                   id="decant_capacity" name="decant_capacity" value="{{ old('decant_capacity', $journeyVoucher->decant_capacity) }}">
                            @error('decant_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_direct_bill" name="is_direct_bill" value="1" 
                                       {{ old('is_direct_bill', $journeyVoucher->is_direct_bill) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_direct_bill">
                                    Direct Bill
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journey-vouchers.show', $journeyVoucher) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-{{ $journeyVoucher->journey_type === 'primary' ? 'primary' : 'success' }}">
                            <i class="fas fa-save me-2"></i>
                            Update Journey
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Calculation Preview
                </h5>
            </div>
            <div class="card-body">
                <div id="calculationPreview">
                    <p class="text-muted">Fill in the form to see calculations</p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Current Values
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Freight Amount:</strong></div>
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
                    <i class="fas fa-history me-2"></i>
                    Journey History
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6 text-end">{{ $journeyVoucher->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Last Updated:</strong></div>
                    <div class="col-6 text-end">{{ $journeyVoucher->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Created By:</strong></div>
                    <div class="col-6 text-end">{{ $journeyVoucher->creator->name }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editJourneyForm');
    const vehicleSelect = document.getElementById('vehicle_id');
    const capacityInput = document.getElementById('capacity');
    const preview = document.getElementById('calculationPreview');

    // Auto-fill capacity when vehicle is selected
    vehicleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.capacity) {
            capacityInput.value = selectedOption.dataset.capacity;
            calculatePreview();
        }
    });

    // Calculate preview when form values change
    form.addEventListener('input', calculatePreview);

    // Initial calculation
    calculatePreview();

    function calculatePreview() {
        const capacity = parseFloat(capacityInput.value) || 0;
        const companyRate = parseFloat(document.getElementById('company_freight_rate').value) || 0;
        const vehicleRate = parseFloat(document.getElementById('vehicle_freight_rate').value) || companyRate;
        const shortageQty = parseFloat(document.getElementById('shortage_quantity').value) || 0;
        const shortageRate = parseFloat(document.getElementById('shortage_rate').value) || 0;
        const companyDeduction = parseFloat(document.getElementById('company_deduction_percentage').value) || 0;
        const vehicleDeduction = parseFloat(document.getElementById('vehicle_deduction_percentage').value) || 0;

        if (capacity > 0 && companyRate > 0) {
            const companyFreight = capacity * companyRate;
            const vehicleFreight = capacity * vehicleRate;
            const shortageAmount = shortageQty * shortageRate;
            const companyDeductionAmount = (companyFreight * companyDeduction) / 100;
            const vehicleDeductionAmount = (vehicleFreight * vehicleDeduction) / 100;
            const commissionAmount = Math.max(0, vehicleDeductionAmount - companyDeductionAmount);
            const totalAmount = vehicleFreight - shortageAmount - vehicleDeductionAmount;

            preview.innerHTML = `
                <div class="row">
                    <div class="col-6"><strong>Company Freight:</strong></div>
                    <div class="col-6">₹${companyFreight.toFixed(2)}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Vehicle Freight:</strong></div>
                    <div class="col-6">₹${vehicleFreight.toFixed(2)}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Shortage Amount:</strong></div>
                    <div class="col-6">₹${shortageAmount.toFixed(2)}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Commission:</strong></div>
                    <div class="col-6">₹${commissionAmount.toFixed(2)}</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6"><strong>Total Amount:</strong></div>
                    <div class="col-6"><strong>₹${totalAmount.toFixed(2)}</strong></div>
                </div>
            `;
        } else {
            preview.innerHTML = '<p class="text-muted">Fill in capacity and freight rate to see calculations</p>';
        }
    }
});
</script>
@endpush
