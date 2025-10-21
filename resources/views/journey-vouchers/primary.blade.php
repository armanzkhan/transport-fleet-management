@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Primary Journey Voucher</h2>
                <p class="text-muted mb-0">Create a new primary journey voucher for cargo transport.</p>
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
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Primary Journey Details
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('journey-vouchers.store-primary') }}" method="POST" id="primaryJourneyForm">
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
                            <div class="form-text">Auto-filled from selected vehicle</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_capacity" class="form-label">Vehicle Max Capacity</label>
                            <input type="number" step="0.01" class="form-control" id="vehicle_capacity" readonly>
                            <div class="form-text">Maximum capacity of selected vehicle</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="capacity_utilization" class="form-label">Capacity Utilization</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="capacity_utilization" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Percentage of vehicle capacity used</div>
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

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <!-- Go To Navigation Buttons -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('date')">
                                <i class="fas fa-calendar me-1"></i>
                                Go To Date
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('carriage')">
                                <i class="fas fa-truck me-1"></i>
                                Go To Carriage
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('company')">
                                <i class="fas fa-building me-1"></i>
                                Go To Company
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('loading_point')">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Go To Loading Point
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('destination')">
                                <i class="fas fa-flag me-1"></i>
                                Go To Destination
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('vrn')">
                                <i class="fas fa-truck me-1"></i>
                                Go To VRN
                            </button>
                        </div>
                        
                        <!-- Search by Invoice -->
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control form-control-sm" id="invoiceSearch" placeholder="Search by Invoice Number">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="searchByInvoice()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Create Primary Journey
                            </button>
                        </div>
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
                    Primary Journey Info
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Main cargo transport journey</li>
                    <li><i class="fas fa-check text-success me-2"></i>Full capacity utilization</li>
                    <li><i class="fas fa-check text-success me-2"></i>Standard freight rates apply</li>
                    <li><i class="fas fa-check text-success me-2"></i>Commission calculations included</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.calculation-preview {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.5rem;
    padding: 1rem;
}

.vehicle-info {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
    padding: 0.75rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.capacity-warning {
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
}

.capacity-danger {
    border-left: 4px solid #dc3545;
    background-color: #f8d7da;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.calculation-section {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.75rem;
    margin-bottom: 0.75rem;
}

.calculation-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('primaryJourneyForm');
    const vehicleSelect = document.getElementById('vehicle_id');
    const capacityInput = document.getElementById('capacity');
    const preview = document.getElementById('calculationPreview');

    // Auto-fill capacity when vehicle is selected
    vehicleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const vehicleCapacityField = document.getElementById('vehicle_capacity');
        
        if (selectedOption.dataset.capacity) {
            const maxCapacity = parseFloat(selectedOption.dataset.capacity);
            vehicleCapacityField.value = maxCapacity;
            capacityInput.value = maxCapacity; // Auto-fill with max capacity
            updateCapacityUtilization();
            calculatePreview();
        } else {
            vehicleCapacityField.value = '';
            capacityInput.value = '';
            document.getElementById('capacity_utilization').value = '';
        }
    });

    // Update capacity utilization when capacity changes
    capacityInput.addEventListener('input', function() {
        updateCapacityUtilization();
        calculatePreview();
    });

    function updateCapacityUtilization() {
        const vehicleCapacity = parseFloat(document.getElementById('vehicle_capacity').value) || 0;
        const currentCapacity = parseFloat(capacityInput.value) || 0;
        const utilizationField = document.getElementById('capacity_utilization');
        
        if (vehicleCapacity > 0) {
            const utilization = (currentCapacity / vehicleCapacity) * 100;
            utilizationField.value = utilization.toFixed(2);
            
            // Add visual feedback for capacity utilization
            if (utilization > 100) {
                capacityInput.classList.add('is-invalid');
                utilizationField.classList.add('is-invalid');
            } else {
                capacityInput.classList.remove('is-invalid');
                utilizationField.classList.remove('is-invalid');
            }
        } else {
            utilizationField.value = '';
        }
    }

    // Calculate preview when form values change
    form.addEventListener('input', calculatePreview);
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const capacity = parseFloat(capacityInput.value) || 0;
        const vehicleCapacity = parseFloat(document.getElementById('vehicle_capacity').value) || 0;
        
        // Check if capacity exceeds vehicle maximum
        if (vehicleCapacity > 0 && capacity > vehicleCapacity) {
            e.preventDefault();
            alert('Capacity cannot exceed vehicle maximum capacity (' + vehicleCapacity + ' tons)');
            capacityInput.focus();
            return false;
        }
        
        // Check if required fields are filled
        const requiredFields = form.querySelectorAll('[required]');
        let hasErrors = false;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                hasErrors = true;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Journey...';
        submitBtn.disabled = true;
    });

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

            const capacityUtilization = parseFloat(document.getElementById('capacity_utilization').value) || 0;
            const utilizationClass = capacityUtilization > 100 ? 'text-danger' : capacityUtilization > 90 ? 'text-warning' : 'text-success';
            
            preview.innerHTML = `
                <div class="mb-3">
                    <h6 class="text-primary mb-2">Journey Summary</h6>
                    <div class="row">
                        <div class="col-6"><strong>Capacity:</strong></div>
                        <div class="col-6">${capacity.toFixed(2)} tons</div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Utilization:</strong></div>
                        <div class="col-6"><span class="${utilizationClass}">${capacityUtilization.toFixed(1)}%</span></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary mb-2">Financial Breakdown</h6>
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
                        <div class="col-6"><strong>Company Deduction:</strong></div>
                        <div class="col-6">₹${(companyFreight * companyDeduction / 100).toFixed(2)}</div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Vehicle Deduction:</strong></div>
                        <div class="col-6">₹${vehicleDeductionAmount.toFixed(2)}</div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Commission:</strong></div>
                        <div class="col-6">₹${commissionAmount.toFixed(2)}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6"><strong>Total Amount:</strong></div>
                        <div class="col-6"><strong class="text-success">₹${totalAmount.toFixed(2)}</strong></div>
                    </div>
                </div>
                
                ${capacityUtilization > 100 ? '<div class="alert alert-warning alert-sm mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Capacity exceeds vehicle maximum!</div>' : ''}
            `;
        } else {
            preview.innerHTML = '<p class="text-muted">Fill in capacity and freight rate to see calculations</p>';
        }
    }
});

// Go To Navigation Functions
function goToNavigation(type) {
    let currentValue = '';
    let searchUrl = '';
    
    switch(type) {
        case 'date':
            currentValue = document.getElementById('journey_date').value;
            searchUrl = `{{ route('journey-vouchers.index') }}?search_date=${currentValue}`;
            break;
        case 'carriage':
            currentValue = document.getElementById('carriage_name').value;
            searchUrl = `{{ route('journey-vouchers.index') }}?search_carriage=${encodeURIComponent(currentValue)}`;
            break;
        case 'company':
            currentValue = document.getElementById('company').value;
            searchUrl = `{{ route('journey-vouchers.index') }}?search_company=${encodeURIComponent(currentValue)}`;
            break;
        case 'loading_point':
            currentValue = document.getElementById('loading_point').value;
            searchUrl = `{{ route('journey-vouchers.index') }}?search_loading_point=${encodeURIComponent(currentValue)}`;
            break;
        case 'destination':
            currentValue = document.getElementById('destination').value;
            searchUrl = `{{ route('journey-vouchers.index') }}?search_destination=${encodeURIComponent(currentValue)}`;
            break;
        case 'vrn':
            const vehicleSelect = document.getElementById('vehicle_id');
            const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            currentValue = selectedOption.text.split(' - ')[0]; // Extract VRN
            searchUrl = `{{ route('journey-vouchers.index') }}?search_vrn=${encodeURIComponent(currentValue)}`;
            break;
    }
    
    if (currentValue) {
        // Open in new tab to preserve current form
        window.open(searchUrl, '_blank');
    } else {
        alert('Please select a value first to search for similar entries.');
    }
}

// Search by Invoice Function
function searchByInvoice() {
    const invoiceNumber = document.getElementById('invoiceSearch').value.trim();
    
    if (invoiceNumber) {
        const searchUrl = `{{ route('journey-vouchers.index') }}?search_invoice=${encodeURIComponent(invoiceNumber)}`;
        // Open in new tab to preserve current form
        window.open(searchUrl, '_blank');
    } else {
        alert('Please enter an invoice number to search.');
    }
}

// Auto-fill invoice number from search
document.getElementById('invoiceSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchByInvoice();
    }
});
</script>
@endpush
