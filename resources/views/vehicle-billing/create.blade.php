@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Create Vehicle Bill</h2>
                <p class="text-muted mb-0">Generate monthly bill for vehicle operations.</p>
            </div>
            <div>
                <a href="{{ route('vehicle-billing.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Bills
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
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Bill Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('vehicle-billing.store') }}" method="POST" id="createBillForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_id" class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" 
                                    id="vehicle_id" name="vehicle_id" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vrn }} - {{ $vehicle->driver_name }} ({{ $vehicle->owner->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
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
                            <label for="bill_munshiyana" class="form-label">Bill Munshiyana</label>
                            <input type="number" step="0.01" class="form-control @error('bill_munshiyana') is-invalid @enderror" 
                                   id="bill_munshiyana" name="bill_munshiyana" value="{{ old('bill_munshiyana', 0) }}">
                            @error('bill_munshiyana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('vehicle-billing.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="createBillBtn" disabled>
                            <i class="fas fa-save me-2"></i>
                            Create Bill
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
                    <i class="fas fa-info-circle me-2"></i>
                    Bill Preview
                </h5>
            </div>
            <div class="card-body">
                <div id="billPreview">
                    <p class="text-muted">Select vehicle and billing month to see preview</p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Important Notes
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Only processed journey vouchers will be included</li>
                    <li><i class="fas fa-check text-success me-2"></i>Advance and expense entries will be calculated</li>
                    <li><i class="fas fa-check text-success me-2"></i>Previous bill balance will be carried forward</li>
                    <li><i class="fas fa-check text-success me-2"></i>Bill can be finalized after creation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Entries Selection Modal -->
<div class="modal fade" id="entriesModal" tabindex="-1" aria-labelledby="entriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entriesModalLabel">Select Entries for Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="entriesContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmEntries">Confirm Selection</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const billingMonthInput = document.getElementById('billing_month');
    const billMunshiyanaInput = document.getElementById('bill_munshiyana');
    const createBillBtn = document.getElementById('createBillBtn');
    const billPreview = document.getElementById('billPreview');
    const entriesModal = new bootstrap.Modal(document.getElementById('entriesModal'));
    const entriesContent = document.getElementById('entriesContent');
    const confirmEntriesBtn = document.getElementById('confirmEntries');

    let selectedFreightEntries = [];
    let selectedAdvanceEntries = [];
    let selectedExpenseEntries = [];

    // Load bill data when vehicle and month are selected
    function loadBillData() {
        const vehicleId = vehicleSelect.value;
        const billingMonth = billingMonthInput.value;

        if (vehicleId && billingMonth) {
            fetch(`{{ route('vehicle-billing.data') }}?vehicle_id=${vehicleId}&billing_month=${billingMonth}`)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        updateBillPreview(data.data);
                        createBillBtn.disabled = false;
                    } else {
                        billPreview.innerHTML = '<p class="text-muted">No data available for selected vehicle and month</p>';
                        createBillBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading bill data:', error);
                    billPreview.innerHTML = '<p class="text-danger">Error loading bill data</p>';
                    createBillBtn.disabled = true;
                });
        } else {
            billPreview.innerHTML = '<p class="text-muted">Select vehicle and billing month to see preview</p>';
            createBillBtn.disabled = true;
        }
    }

    function updateBillPreview(data) {
        const totalFreight = data.freight_entries.reduce((sum, entry) => sum + parseFloat(entry.total_amount), 0);
        const totalAdvance = data.advance_entries.reduce((sum, entry) => sum + parseFloat(entry.amount), 0);
        const totalExpense = data.expense_entries.reduce((sum, entry) => sum + parseFloat(entry.amount), 0);
        const billMunshiyana = parseFloat(billMunshiyanaInput.value) || 0;
        const grossProfit = totalFreight - totalAdvance - totalExpense;
        const netProfit = grossProfit - billMunshiyana;

        billPreview.innerHTML = `
            <div class="row mb-2">
                <div class="col-6"><strong>Freight Entries:</strong></div>
                <div class="col-6">${data.freight_entries.length}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Total Freight:</strong></div>
                <div class="col-6">₹${totalFreight.toFixed(2)}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Advance Entries:</strong></div>
                <div class="col-6">${data.advance_entries.length}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Total Advance:</strong></div>
                <div class="col-6">₹${totalAdvance.toFixed(2)}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Expense Entries:</strong></div>
                <div class="col-6">${data.expense_entries.length}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Total Expense:</strong></div>
                <div class="col-6">₹${totalExpense.toFixed(2)}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong>Bill Munshiyana:</strong></div>
                <div class="col-6">₹${billMunshiyana.toFixed(2)}</div>
            </div>
            <hr>
            <div class="row mb-2">
                <div class="col-6"><strong>Gross Profit:</strong></div>
                <div class="col-6">₹${grossProfit.toFixed(2)}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Net Profit:</strong></div>
                <div class="col-6"><strong class="${netProfit >= 0 ? 'text-success' : 'text-danger'}">₹${netProfit.toFixed(2)}</strong></div>
            </div>
        `;
    }

    // Event listeners
    vehicleSelect.addEventListener('change', loadBillData);
    billingMonthInput.addEventListener('change', loadBillData);
    billMunshiyanaInput.addEventListener('input', loadBillData);

    // Form submission
    document.getElementById('createBillForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const vehicleId = vehicleSelect.value;
        const billingMonth = billingMonthInput.value;

        if (!vehicleId || !billingMonth) {
            alert('Please select vehicle and billing month.');
            return;
        }

        // Load entries for selection
        fetch(`{{ route('vehicle-billing.data') }}?vehicle_id=${vehicleId}&billing_month=${billingMonth}`)
            .then(response => response.json())
            .then(data => {
                if (data.data) {
                    showEntriesModal(data.data);
                } else {
                    alert('No data available for selected vehicle and month.');
                }
            })
            .catch(error => {
                console.error('Error loading entries:', error);
                alert('Error loading entries data.');
            });
    });

    function showEntriesModal(data) {
        selectedFreightEntries = data.freight_entries.map(entry => entry.id);
        selectedAdvanceEntries = data.advance_entries.map(entry => entry.id);
        selectedExpenseEntries = data.expense_entries.map(entry => entry.id);

        entriesContent.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <h6>Freight Entries (${data.freight_entries.length})</h6>
                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                        ${data.freight_entries.map(entry => `
                            <div class="form-check">
                                <input class="form-check-input freight-checkbox" type="checkbox" value="${entry.id}" checked>
                                <label class="form-check-label">
                                    ${entry.journey_number} - ₹${parseFloat(entry.total_amount).toFixed(2)}
                                </label>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>Advance Entries (${data.advance_entries.length})</h6>
                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                        ${data.advance_entries.map(entry => `
                            <div class="form-check">
                                <input class="form-check-input advance-checkbox" type="checkbox" value="${entry.id}" checked>
                                <label class="form-check-label">
                                    ${entry.cash_book_number} - ₹${parseFloat(entry.amount).toFixed(2)}
                                </label>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>Expense Entries (${data.expense_entries.length})</h6>
                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                        ${data.expense_entries.map(entry => `
                            <div class="form-check">
                                <input class="form-check-input expense-checkbox" type="checkbox" value="${entry.id}" checked>
                                <label class="form-check-label">
                                    ${entry.cash_book_number} - ₹${parseFloat(entry.amount).toFixed(2)}
                                </label>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

        entriesModal.show();
    }

    confirmEntriesBtn.addEventListener('click', function() {
        // Collect selected entries
        const freightCheckboxes = document.querySelectorAll('.freight-checkbox:checked');
        const advanceCheckboxes = document.querySelectorAll('.advance-checkbox:checked');
        const expenseCheckboxes = document.querySelectorAll('.expense-checkbox:checked');

        selectedFreightEntries = Array.from(freightCheckboxes).map(cb => cb.value);
        selectedAdvanceEntries = Array.from(advanceCheckboxes).map(cb => cb.value);
        selectedExpenseEntries = Array.from(expenseCheckboxes).map(cb => cb.value);

        // Add hidden inputs to form
        const form = document.getElementById('createBillForm');
        
        // Remove existing hidden inputs
        document.querySelectorAll('input[name^="freight_entries"], input[name^="advance_entries"], input[name^="expense_entries"]').forEach(input => input.remove());

        // Add new hidden inputs
        selectedFreightEntries.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'freight_entries[]';
            input.value = id;
            form.appendChild(input);
        });

        selectedAdvanceEntries.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'advance_entries[]';
            input.value = id;
            form.appendChild(input);
        });

        selectedExpenseEntries.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'expense_entries[]';
            input.value = id;
            form.appendChild(input);
        });

        entriesModal.hide();
        form.submit();
    });
});
</script>
@endpush
