@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Edit Secondary Journey Voucher</h2>
                <p class="text-muted mb-0">Journey Number: <strong>{{ $secondaryJourneyVoucher->journey_number }}</strong></p>
            </div>
            <div>
                <a href="{{ route('secondary-journey-vouchers.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
                <a href="{{ route('secondary-journey-vouchers.show', $secondaryJourneyVoucher) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye me-2"></i>
                    View
                </a>
            </div>
        </div>
    </div>
</div>

<form id="secondaryJvForm" method="POST" action="{{ route('secondary-journey-vouchers.update', $secondaryJourneyVoucher) }}">
    @csrf
    @method('PUT')
    
    <!-- Header Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Secondary Journey Voucher Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="contractor_name" class="form-label">Contractor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                                   value="{{ old('contractor_name', $secondaryJourneyVoucher->contractor_name) }}" required>
                            @error('contractor_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                            <select class="form-select" id="company" name="company" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->name }}" {{ old('company', $secondaryJourneyVoucher->company) == $company->name ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="journey_date" class="form-label">Journey Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="journey_date" name="journey_date" 
                                   value="{{ old('journey_date', $secondaryJourneyVoucher->journey_date->format('Y-m-d')) }}" required>
                            @error('journey_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Rate Apply Panel -->
    <div class="row mb-4" id="autoRatePanel" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="card-title mb-0 text-warning">
                        <i class="fas fa-magic me-2"></i>
                        Auto Rate Apply Panel
                    </h6>
                </div>
                <div class="card-body">
                    <div id="autoRateRoutes"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entries Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>
                            Journey Entries
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" onclick="addEntry()">
                            <i class="fas fa-plus me-1"></i>
                            Add Entry
                        </button>
                    </div>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="entriesTableBody">
                                @foreach($secondaryJourneyVoucher->entries as $index => $entry)
                                <tr class="entry-row">
                                    <td class="sno">{{ $index + 1 }}</td>
                                    <td>
                                        <select class="form-select form-select-sm vrn-select" name="entries[{{ $index }}][vrn]" required>
                                            <option value="">Select VRN</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->vrn }}" {{ $entry->vrn == $vehicle->vrn ? 'selected' : '' }}>
                                                    {{ $vehicle->vrn }} - {{ $vehicle->owner->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="entries[{{ $index }}][invoice_number]" 
                                               value="{{ $entry->invoice_number }}" placeholder="Invoice No." required>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm loading-point-select" name="entries[{{ $index }}][loading_point]" required>
                                            <option value="">Select Loading Point</option>
                                            @foreach($loadingPoints as $point)
                                                <option value="{{ $point->name }}" {{ $entry->loading_point == $point->name ? 'selected' : '' }}>
                                                    {{ $point->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm destination-select" name="entries[{{ $index }}][destination]" required>
                                            <option value="">Select Destination</option>
                                            @foreach($destinations as $destination)
                                                <option value="{{ $destination->name }}" {{ $entry->destination == $destination->name ? 'selected' : '' }}>
                                                    {{ $destination->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm product-select" name="entries[{{ $index }}][product]" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->name }}" {{ $entry->product == $product->name ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm rate-input" name="entries[{{ $index }}][rate]" 
                                               value="{{ $entry->rate }}" placeholder="0.00" step="0.01" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm load-quantity-input" name="entries[{{ $index }}][load_quantity]" 
                                               value="{{ $entry->load_quantity }}" placeholder="0.00" step="0.01" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm freight-display" 
                                               value="{{ $entry->freight }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm shortage-quantity-input" name="entries[{{ $index }}][shortage_quantity]" 
                                               value="{{ $entry->shortage_quantity }}" placeholder="0.00" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm shortage-amount-input" name="entries[{{ $index }}][shortage_amount]" 
                                               value="{{ $entry->shortage_amount }}" placeholder="0.00" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm company-deduction-input" name="entries[{{ $index }}][company_deduction]" 
                                               value="{{ $entry->company_deduction }}" placeholder="0.00" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm vehicle-commission-input" name="entries[{{ $index }}][vehicle_commission]" 
                                               value="{{ $entry->vehicle_commission }}" placeholder="0.00" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm total-display" 
                                               value="{{ $entry->net_amount }}" readonly>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input pr04-checkbox" type="checkbox" name="entries[{{ $index }}][pr04]" value="1" 
                                                   {{ $entry->pr04 ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-entry" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

    <!-- Summary Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total Freight</h6>
                                <h4 class="text-primary" id="totalFreight">₨{{ number_format($secondaryJourneyVoucher->total_freight, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total Shortage</h6>
                                <h4 class="text-danger" id="totalShortage">₨{{ number_format($secondaryJourneyVoucher->total_shortage, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Company Deduction</h6>
                                <h4 class="text-warning" id="totalCompanyDeduction">₨{{ number_format($secondaryJourneyVoucher->total_company_deduction, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Net Amount</h6>
                                <h4 class="text-success" id="netAmount">₨{{ number_format($secondaryJourneyVoucher->net_amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('secondary-journey-vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </a>
                <div>
                    <button type="button" class="btn btn-outline-info me-2" onclick="calculateSummary()">
                        <i class="fas fa-calculator me-2"></i>
                        Calculate
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Update Secondary JV
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Entry Template -->
<template id="entryTemplate">
    <tr class="entry-row">
        <td class="sno">1</td>
        <td>
            <select class="form-select form-select-sm vrn-select" name="entries[INDEX][vrn]" required>
                <option value="">Select VRN</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->vrn }}">{{ $vehicle->vrn }} - {{ $vehicle->owner->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="entries[INDEX][invoice_number]" placeholder="Invoice No." required>
        </td>
        <td>
            <select class="form-select form-select-sm loading-point-select" name="entries[INDEX][loading_point]" required>
                <option value="">Select Loading Point</option>
                @foreach($loadingPoints as $point)
                    <option value="{{ $point->name }}">{{ $point->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm destination-select" name="entries[INDEX][destination]" required>
                <option value="">Select Destination</option>
                @foreach($destinations as $destination)
                    <option value="{{ $destination->name }}">{{ $destination->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm product-select" name="entries[INDEX][product]" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->name }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm rate-input" name="entries[INDEX][rate]" 
                   placeholder="0.00" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm load-quantity-input" name="entries[INDEX][load_quantity]" 
                   placeholder="0.00" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm freight-display" readonly>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm shortage-quantity-input" name="entries[INDEX][shortage_quantity]" 
                   placeholder="0.00" step="0.01" min="0">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm shortage-amount-input" name="entries[INDEX][shortage_amount]" 
                   placeholder="0.00" step="0.01" min="0">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm company-deduction-input" name="entries[INDEX][company_deduction]" 
                   placeholder="0.00" step="0.01" min="0">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm vehicle-commission-input" name="entries[INDEX][vehicle_commission]" 
                   placeholder="0.00" step="0.01" min="0">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm total-display" readonly>
        </td>
        <td>
            <div class="form-check">
                <input class="form-check-input pr04-checkbox" type="checkbox" name="entries[INDEX][pr04]" value="1">
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-entry" title="Remove Entry">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
let entryIndex = {{ $secondaryJourneyVoucher->entries->count() }};

// Add new entry
function addEntry() {
    const template = document.getElementById('entryTemplate');
    const clone = template.content.cloneNode(true);
    
    // Replace INDEX with current entry index
    const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, entryIndex);
    
    // Insert into table
    document.getElementById('entriesTableBody').insertAdjacentHTML('beforeend', html);
    
    // Add event listeners to new row
    const newRow = document.querySelector('#entriesTableBody tr:last-child');
    addEntryEventListeners(newRow);
    
    // Update row numbers
    updateRowNumbers();
    
    // Update auto rate panel
    updateAutoRatePanel();
    
    entryIndex++;
}

// Add event listeners to entry row
function addEntryEventListeners(row) {
    const rateInput = row.querySelector('.rate-input');
    const loadQuantityInput = row.querySelector('.load-quantity-input');
    const shortageQuantityInput = row.querySelector('.shortage-quantity-input');
    const shortageAmountInput = row.querySelector('.shortage-amount-input');
    const companyDeductionInput = row.querySelector('.company-deduction-input');
    const vehicleCommissionInput = row.querySelector('.vehicle-commission-input');
    const removeBtn = row.querySelector('.remove-entry');
    
    // Calculate freight when rate or quantity changes
    [rateInput, loadQuantityInput].forEach(input => {
        input.addEventListener('input', () => calculateFreight(row));
    });
    
    // Calculate shortage amount when quantity changes
    shortageQuantityInput.addEventListener('input', () => calculateShortageAmount(row));
    
    // Calculate total when any amount changes
    [shortageAmountInput, companyDeductionInput, vehicleCommissionInput].forEach(input => {
        input.addEventListener('input', () => calculateTotal(row));
    });
    
    // Remove entry
    removeBtn.addEventListener('click', () => removeEntry(row));
    
    // Update auto rate panel when route changes
    [row.querySelector('.loading-point-select'), row.querySelector('.destination-select')].forEach(select => {
        select.addEventListener('change', () => updateAutoRatePanel());
    });
}

// Calculate freight
function calculateFreight(row) {
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const quantity = parseFloat(row.querySelector('.load-quantity-input').value) || 0;
    const freight = rate * quantity;
    
    row.querySelector('.freight-display').value = freight.toFixed(2);
    calculateTotal(row);
}

// Calculate shortage amount
function calculateShortageAmount(row) {
    const quantity = parseFloat(row.querySelector('.shortage-quantity-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const shortageAmount = quantity * rate;
    
    row.querySelector('.shortage-amount-input').value = shortageAmount.toFixed(2);
    calculateTotal(row);
}

// Calculate total
function calculateTotal(row) {
    const freight = parseFloat(row.querySelector('.freight-display').value) || 0;
    const shortageAmount = parseFloat(row.querySelector('.shortage-amount-input').value) || 0;
    const companyDeduction = parseFloat(row.querySelector('.company-deduction-input').value) || 0;
    const vehicleCommission = parseFloat(row.querySelector('.vehicle-commission-input').value) || 0;
    
    const total = freight - shortageAmount - companyDeduction;
    
    row.querySelector('.total-display').value = total.toFixed(2);
    calculateSummary();
}

// Remove entry
function removeEntry(row) {
    row.remove();
    updateRowNumbers();
    updateAutoRatePanel();
    calculateSummary();
}

// Update row numbers
function updateRowNumbers() {
    const rows = document.querySelectorAll('#entriesTableBody .entry-row');
    rows.forEach((row, index) => {
        row.querySelector('.sno').textContent = index + 1;
    });
}

// Calculate summary
function calculateSummary() {
    let totalFreight = 0;
    let totalShortage = 0;
    let totalCompanyDeduction = 0;
    let totalVehicleCommission = 0;
    
    document.querySelectorAll('#entriesTableBody .entry-row').forEach(row => {
        totalFreight += parseFloat(row.querySelector('.freight-display').value) || 0;
        totalShortage += parseFloat(row.querySelector('.shortage-amount-input').value) || 0;
        totalCompanyDeduction += parseFloat(row.querySelector('.company-deduction-input').value) || 0;
        totalVehicleCommission += parseFloat(row.querySelector('.vehicle-commission-input').value) || 0;
    });
    
    const netAmount = totalFreight - totalShortage - totalCompanyDeduction;
    
    document.getElementById('totalFreight').textContent = '₨' + totalFreight.toFixed(2);
    document.getElementById('totalShortage').textContent = '₨' + totalShortage.toFixed(2);
    document.getElementById('totalCompanyDeduction').textContent = '₨' + totalCompanyDeduction.toFixed(2);
    document.getElementById('netAmount').textContent = '₨' + netAmount.toFixed(2);
}

// Update auto rate panel
function updateAutoRatePanel() {
    const routes = new Map();
    
    document.querySelectorAll('#entriesTableBody .entry-row').forEach(row => {
        const loadingPoint = row.querySelector('.loading-point-select').value;
        const destination = row.querySelector('.destination-select').value;
        
        if (loadingPoint && destination) {
            const route = `${loadingPoint} → ${destination}`;
            if (!routes.has(route)) {
                routes.set(route, {
                    loadingPoint,
                    destination,
                    rates: []
                });
            }
        }
    });
    
    if (routes.size > 0) {
        document.getElementById('autoRatePanel').style.display = 'block';
        
        let html = '';
        routes.forEach((data, route) => {
            html += `
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">${route}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control auto-rate-input" 
                               data-loading-point="${data.loadingPoint}" 
                               data-destination="${data.destination}"
                               placeholder="Enter rate" step="0.01" min="0">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-warning btn-sm apply-rate-btn"
                                data-loading-point="${data.loadingPoint}" 
                                data-destination="${data.destination}">
                            Apply to All
                        </button>
                    </div>
                </div>
            `;
        });
        
        document.getElementById('autoRateRoutes').innerHTML = html;
        
        // Add event listeners for apply buttons
        document.querySelectorAll('.apply-rate-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const loadingPoint = this.dataset.loadingPoint;
                const destination = this.dataset.destination;
                const rateInput = this.parentElement.previousElementSibling.querySelector('.auto-rate-input');
                const rate = parseFloat(rateInput.value) || 0;
                
                if (rate > 0) {
                    applyRateToRoute(loadingPoint, destination, rate);
                }
            });
        });
    } else {
        document.getElementById('autoRatePanel').style.display = 'none';
    }
}

// Apply rate to route
function applyRateToRoute(loadingPoint, destination, rate) {
    document.querySelectorAll('#entriesTableBody .entry-row').forEach(row => {
        const rowLoadingPoint = row.querySelector('.loading-point-select').value;
        const rowDestination = row.querySelector('.destination-select').value;
        
        if (rowLoadingPoint === loadingPoint && rowDestination === destination) {
            row.querySelector('.rate-input').value = rate.toFixed(2);
            calculateFreight(row);
        }
    });
}

// Initialize existing entries
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#entriesTableBody .entry-row').forEach(row => {
        addEntryEventListeners(row);
    });
    updateAutoRatePanel();
});
</script>
@endpush
@endsection
