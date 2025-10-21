@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            Daily Cash Book Entry
                        </h5>
                        <p class="text-muted mb-0 mt-1">Record all daily cash transactions in one entry</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info fs-6" id="entryDateDisplay">{{ date('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Information Alert -->
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="alert-heading mb-1">How to Create Daily Entry</h6>
                            <ul class="mb-0 small">
                                <li><strong>Select Date:</strong> Choose the date for your cash transactions</li>
                                <li><strong>Add Receives:</strong> Click "Add Receive" to record money coming in (left side)</li>
                                <li><strong>Add Payments:</strong> Click "Add Payment" to record money going out (right side)</li>
                                <li><strong>Review Summary:</strong> Check the calculated balances at the bottom</li>
                                <li><strong>Save Entry:</strong> All transactions will be saved together</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Display Errors -->
                @if($errors->any())
                    <div class="alert alert-danger border-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="alert-heading mb-1">Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success border-0">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('cash-books.store-daily') }}" id="dailyEntryForm">
                    @csrf
                    
                    <!-- Date and Balance Row -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="entry_date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('entry_date') is-invalid @enderror" 
                                   id="entry_date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Previous Day Balance</label>
                            <div class="form-control-plaintext fw-bold text-primary" id="previous_balance">
                                <i class="fas fa-wallet me-1"></i>
                                PKR {{ number_format($todaysPreviousBalance ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Receives</label>
                            <div class="form-control-plaintext fw-bold text-success" id="total_receives">
                                <i class="fas fa-plus-circle me-1"></i>
                                PKR 0.00
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Closing Balance</label>
                            <div class="form-control-plaintext fw-bold text-info" id="closing_balance">
                                <i class="fas fa-calculator me-1"></i>
                                PKR 0.00
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- RECEIVE TRANSACTIONS -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success bg-gradient text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-arrow-up me-2"></i>
                                            Cash Receives
                                        </h6>
                                        <button type="button" class="btn btn-light btn-sm" id="addReceive" onclick="addReceiveRow()">
                                            <i class="fas fa-plus me-1"></i>
                                            Add Receive
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="25%">Account</th>
                                                    <th width="20%">Vehicle</th>
                                                    <th width="35%">Description</th>
                                                    <th width="15%">Amount</th>
                                                    <th width="5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="receivesBody">
                                                <!-- Receive transactions will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-success">
                                                    <th colspan="3" class="text-end">Total Receives:</th>
                                                    <th id="totalReceivesFooter">PKR 0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAYMENT TRANSACTIONS -->
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning bg-gradient text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-arrow-down me-2"></i>
                                            Cash Payments
                                        </h6>
                                        <button type="button" class="btn btn-dark btn-sm" id="addPayment" onclick="addPaymentRow()">
                                            <i class="fas fa-plus me-1"></i>
                                            Add Payment
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="15%">Type</th>
                                                    <th width="20%">Account</th>
                                                    <th width="15%">Vehicle</th>
                                                    <th width="30%">Description</th>
                                                    <th width="15%">Amount</th>
                                                    <th width="5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="paymentsBody">
                                                <!-- Payment transactions will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-warning">
                                                    <th colspan="4" class="text-end">Total Payments:</th>
                                                    <th id="totalPaymentsFooter">PKR 0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary and Submit -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="fw-bold text-primary fs-5">Opening Balance</div>
                                            <div class="h4 text-primary" id="summaryOpening">PKR 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="fw-bold text-success fs-5">Total Receives</div>
                                            <div class="h4 text-success" id="summaryReceives">PKR 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="fw-bold text-danger fs-5">Total Payments</div>
                                            <div class="h4 text-danger" id="summaryPayments">PKR 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="fw-bold text-info fs-5">Closing Balance</div>
                                            <div class="h4 text-info" id="summaryClosing">PKR 0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('cash-books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Save Daily Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden templates -->
<template id="receiveTemplate">
    <tr>
        <td>
            <select class="form-select form-select-sm" name="receives[INDEX][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="receives[INDEX][vehicle_id]">
                <option value="">Select Vehicle</option>
                @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->vrn }} - {{ $vehicle->owner->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="receives[INDEX][description]" placeholder="Description" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm amount-input" name="receives[INDEX][amount]" placeholder="0.00" step="0.01" min="0.01" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<template id="paymentTemplate">
    <tr>
        <td>
            <select class="form-select form-select-sm" name="payments[INDEX][payment_type]" required>
                <option value="">Type</option>
                <option value="Advance">Advance</option>
                <option value="Expense">Expense</option>
                <option value="Shortage">Shortage</option>
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="payments[INDEX][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="payments[INDEX][vehicle_id]">
                <option value="">Vehicle</option>
                @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->vrn }} - {{ $vehicle->owner->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="payments[INDEX][description]" placeholder="Description" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm amount-input" name="payments[INDEX][amount]" placeholder="0.00" step="0.01" min="0.01" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
// Global variables
let previousBalance = {{ $todaysPreviousBalance ?? 0 }};

// Global functions that can be called from onclick handlers
window.addReceiveRow = function() {
    const receiveIndex = document.querySelectorAll('#receivesBody tr').length;
    const template = document.getElementById('receiveTemplate');
    
    if (!template) {
        alert('Error: receiveTemplate not found');
        return;
    }
    
    const clone = template.content.cloneNode(true);
    const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, receiveIndex);
    document.getElementById('receivesBody').insertAdjacentHTML('beforeend', html);
    
    // Add event listeners to new row
    const newRow = document.getElementById('receivesBody').lastElementChild;
    const amountInput = newRow.querySelector('.amount-input');
    const removeBtn = newRow.querySelector('.remove-row');
    
    if (amountInput) {
        amountInput.addEventListener('input', updateSummary);
    }
    
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            newRow.remove();
            updateSummary();
        });
    }
};

window.addPaymentRow = function() {
    const paymentIndex = document.querySelectorAll('#paymentsBody tr').length;
    const template = document.getElementById('paymentTemplate');
    
    if (!template) {
        alert('Error: paymentTemplate not found');
        return;
    }
    
    const clone = template.content.cloneNode(true);
    const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, paymentIndex);
    document.getElementById('paymentsBody').insertAdjacentHTML('beforeend', html);
    
    // Add event listeners to new row
    const newRow = document.getElementById('paymentsBody').lastElementChild;
    const amountInput = newRow.querySelector('.amount-input');
    const removeBtn = newRow.querySelector('.remove-row');
    
    if (amountInput) {
        amountInput.addEventListener('input', updateSummary);
    }
    
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            newRow.remove();
            updateSummary();
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Load previous day balance
    loadPreviousBalance();
    
    // Date change handler
    document.getElementById('entry_date').addEventListener('change', function() {
        loadPreviousBalance();
        updateDateDisplay();
    });
    
    function updateDateDisplay() {
        const date = new Date(document.getElementById('entry_date').value);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById('entryDateDisplay').textContent = date.toLocaleDateString('en-US', options);
    }
    
    function loadPreviousBalance() {
        const entryDate = document.getElementById('entry_date').value;
        if (entryDate) {
            console.log('Loading previous balance for date:', entryDate);
            
            fetch(`{{ route('cash-books.previous-balance') }}?date=${entryDate}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Previous balance data:', data);
                    previousBalance = data.balance;
                    document.getElementById('previous_balance').innerHTML = 
                        `<i class="fas fa-wallet me-1"></i>PKR ${formatNumber(previousBalance)}`;
                    updateSummary();
                })
                .catch(error => {
                    console.error('Error loading previous balance:', error);
                    document.getElementById('previous_balance').innerHTML = 
                        `<i class="fas fa-wallet me-1"></i><span class="text-danger">Error: ${error.message}</span>`;
                });
        }
    }
    

    
    function updateSummary() {
        const receiveAmounts = Array.from(document.querySelectorAll('#receivesBody .amount-input'))
            .map(input => parseFloat(input.value) || 0);
        const paymentAmounts = Array.from(document.querySelectorAll('#paymentsBody .amount-input'))
            .map(input => parseFloat(input.value) || 0);
        
        const totalReceives = receiveAmounts.reduce((sum, amount) => sum + amount, 0);
        const totalPayments = paymentAmounts.reduce((sum, amount) => sum + amount, 0);
        const closingBalance = previousBalance + totalReceives - totalPayments;
        
        // Update all displays
        document.getElementById('totalReceivesFooter').textContent = `PKR ${formatNumber(totalReceives)}`;
        document.getElementById('totalPaymentsFooter').textContent = `PKR ${formatNumber(totalPayments)}`;
        document.getElementById('total_receives').innerHTML = `<i class="fas fa-plus-circle me-1"></i>PKR ${formatNumber(totalReceives)}`;
        document.getElementById('closing_balance').innerHTML = `<i class="fas fa-calculator me-1"></i>PKR ${formatNumber(closingBalance)}`;
        
        // Update summary section
        document.getElementById('summaryOpening').textContent = `PKR ${formatNumber(previousBalance)}`;
        document.getElementById('summaryReceives').textContent = `PKR ${formatNumber(totalReceives)}`;
        document.getElementById('summaryPayments').textContent = `PKR ${formatNumber(totalPayments)}`;
        document.getElementById('summaryClosing').textContent = `PKR ${formatNumber(closingBalance)}`;
        
        // Color code closing balance
        const closingElement = document.getElementById('summaryClosing');
        closingElement.className = closingBalance >= 0 ? 'h4 text-success' : 'h4 text-danger';
    }
    
    function formatNumber(num) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    // Form validation
    document.getElementById('dailyEntryForm').addEventListener('submit', function(e) {
        const receiveRows = document.querySelectorAll('#receivesBody tr').length;
        const paymentRows = document.querySelectorAll('#paymentsBody tr').length;
        
        console.log('Form submitted with:', receiveRows, 'receives and', paymentRows, 'payments');
        
        if (receiveRows === 0 && paymentRows === 0) {
            e.preventDefault();
            alert('Please add at least one transaction (receive or payment).');
            return false;
        }
        
        // Check if all required fields in visible rows are filled
        let hasErrors = false;
        const allRows = [...document.querySelectorAll('#receivesBody tr'), ...document.querySelectorAll('#paymentsBody tr')];
        
        allRows.forEach(row => {
            const requiredFields = row.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasErrors = true;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
        });
        
        // Also check the entry date
        const entryDateField = document.getElementById('entry_date');
        if (!entryDateField.value.trim()) {
            entryDateField.classList.add('is-invalid');
            hasErrors = true;
        } else {
            entryDateField.classList.remove('is-invalid');
        }
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with red borders).');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        submitBtn.disabled = true;
        
        console.log('Form validation passed, submitting...');
    });
    
    // Don't add initial rows automatically - let user choose what they need
    
    // Initialize display
    updateDateDisplay();
    updateSummary(); // Update summary with initial previous balance
});


</script>
@endpush