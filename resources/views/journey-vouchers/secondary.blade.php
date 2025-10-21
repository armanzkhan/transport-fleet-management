@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Secondary Journey Voucher</h2>
                <p class="text-muted mb-0">Create a new secondary journey voucher for return cargo transport.</p>
            </div>
            <div>
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
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Secondary Journey Details
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('journey-vouchers.store-secondary') }}" method="POST" id="secondaryJourneyForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="journey_date" class="form-label">Journey Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('journey_date') is-invalid @enderror" 
                                   id="journey_date" name="journey_date" value="{{ old('journey_date', date('Y-m-d')) }}" required>
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
                                            {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
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
                            <select class="form-select @error('carriage_name') is-invalid @enderror" 
                                    id="carriage_name" name="carriage_name" required>
                                <option value="">Select Carriage</option>
                                @foreach($carriages as $carriage)
                                    <option value="{{ $carriage->name }}" 
                                            {{ old('carriage_name') == $carriage->name ? 'selected' : '' }}>
                                        {{ $carriage->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                    <option value="{{ $point->name }}" {{ old('loading_point') == $point->name ? 'selected' : '' }}>
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
                                    <option value="{{ $destination->name }}" {{ old('destination') == $destination->name ? 'selected' : '' }}>
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
                                    <option value="{{ $product->name }}" {{ old('product') == $product->name ? 'selected' : '' }}>
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
                                    <option value="{{ $company->name }}" {{ old('company') == $company->name ? 'selected' : '' }}>
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
                                   id="capacity" name="capacity" value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_freight_rate" class="form-label">Company Freight Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('company_freight_rate') is-invalid @enderror" 
                                   id="company_freight_rate" name="company_freight_rate" value="{{ old('company_freight_rate') }}" required>
                            @error('company_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_freight_rate" class="form-label">Vehicle Freight Rate</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_freight_rate') is-invalid @enderror" 
                                   id="vehicle_freight_rate" name="vehicle_freight_rate" value="{{ old('vehicle_freight_rate') }}">
                            @error('vehicle_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="shortage_quantity" class="form-label">Shortage Quantity</label>
                            <input type="number" step="0.01" class="form-control @error('shortage_quantity') is-invalid @enderror" 
                                   id="shortage_quantity" name="shortage_quantity" value="{{ old('shortage_quantity', 0) }}">
                            @error('shortage_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="shortage_rate" class="form-label">Shortage Rate</label>
                            <input type="number" step="0.01" class="form-control @error('shortage_rate') is-invalid @enderror" 
                                   id="shortage_rate" name="shortage_rate" value="{{ old('shortage_rate', 0) }}">
                            @error('shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_deduction_percentage" class="form-label">Company Deduction %</label>
                            <input type="number" step="0.01" class="form-control @error('company_deduction_percentage') is-invalid @enderror" 
                                   id="company_deduction_percentage" name="company_deduction_percentage" value="{{ old('company_deduction_percentage', 0) }}">
                            @error('company_deduction_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_deduction_percentage" class="form-label">Vehicle Deduction %</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_deduction_percentage') is-invalid @enderror" 
                                   id="vehicle_deduction_percentage" name="vehicle_deduction_percentage" value="{{ old('vehicle_deduction_percentage', 0) }}">
                            @error('vehicle_deduction_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}">
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="billing_month" class="form-label">Billing Month <span class="text-danger">*</span></label>
                            <input type="month" class="form-control @error('billing_month') is-invalid @enderror" 
                                   id="billing_month" name="billing_month" value="{{ old('billing_month', date('Y-m')) }}" required>
                            @error('billing_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="decant_capacity" class="form-label">Decant Capacity</label>
                            <input type="number" step="0.01" class="form-control @error('decant_capacity') is-invalid @enderror" 
                                   id="decant_capacity" name="decant_capacity" value="{{ old('decant_capacity') }}">
                            @error('decant_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_direct_bill" name="is_direct_bill" value="1" {{ old('is_direct_bill') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_direct_bill">
                                    Direct Bill
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Create Secondary Journey
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
                    Secondary Journey Info
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Return cargo transport journey</li>
                    <li><i class="fas fa-check text-success me-2"></i>May have different rates</li>
                    <li><i class="fas fa-check text-success me-2"></i>Optimized for return trips</li>
                    <li><i class="fas fa-check text-success me-2"></i>Commission calculations included</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('secondaryJourneyForm');
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
