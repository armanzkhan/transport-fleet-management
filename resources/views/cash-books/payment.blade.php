@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-minus-circle me-2 text-warning"></i>
                            Cash Book - Payment Entry
                        </h5>
                        <p class="text-muted mb-0 mt-1">Record cash payment transactions</p>
                    </div>
                    <a href="{{ route('cash-books.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>
                        View All Entries
                    </a>
                </div>
            </div>
            <div class="card-body">
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

                <form method="POST" action="{{ route('cash-books.store-payment') }}" id="paymentForm">
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
                            <label class="form-label">Total Payments</label>
                            <div class="form-control-plaintext fw-bold text-danger" id="total_payments">
                                <i class="fas fa-minus-circle me-1"></i>
                                PKR 0.00
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Remaining Cash</label>
                            <div class="form-control-plaintext fw-bold text-info" id="remaining_cash">
                                <i class="fas fa-calculator me-1"></i>
                                PKR {{ number_format($todaysPreviousBalance ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-gradient text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-arrow-down me-2"></i>
                                    Cash Payment Transactions
                                </h6>
                                <button type="button" class="btn btn-dark btn-sm" onclick="addPaymentTransaction()">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Transaction
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
                                    <tbody id="transactionsBody">
                                        <!-- Transactions will be added here -->
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
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('cash-books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>
                            Save Payment Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden template -->
<template id="transactionTemplate">
    <tr>
        <td>
            <select class="form-select form-select-sm" name="transactions[INDEX][payment_type]" required>
                <option value="">Type</option>
                <option value="Advance">Advance</option>
                <option value="Expense">Expense</option>
                <option value="Shortage">Shortage</option>
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="transactions[INDEX][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="transactions[INDEX][vehicle_id]">
                <option value="">Vehicle</option>
                @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->vrn }} - {{ $vehicle->owner->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="transactions[INDEX][description]" placeholder="Description" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm amount-input" name="transactions[INDEX][amount]" placeholder="0.00" step="0.01" min="0.01" required>
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
document.addEventListener('DOMContentLoaded', function() {
    let previousBalance = {{ $todaysPreviousBalance ?? 0 }};
    
    // Load previous day balance
    loadPreviousBalance();
    
    // Date change handler
    document.getElementById('entry_date').addEventListener('change', function() {
        loadPreviousBalance();
    });
    
    function loadPreviousBalance() {
        const entryDate = document.getElementById('entry_date').value;
        if (entryDate) {
            fetch(`{{ route('cash-books.previous-balance') }}?date=${entryDate}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
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
    
    // Make function global so it can be called from onclick
    window.addPaymentTransaction = function() {
        const transactionIndex = document.querySelectorAll('#transactionsBody tr').length;
        const template = document.getElementById('transactionTemplate');
        
        if (!template) {
            alert('Error: transactionTemplate not found');
            return;
        }
        
        const clone = template.content.cloneNode(true);
        const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, transactionIndex);
        document.getElementById('transactionsBody').insertAdjacentHTML('beforeend', html);
        
        // Add event listeners to new row
        const newRow = document.getElementById('transactionsBody').lastElementChild;
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
    
    function updateSummary() {
        const paymentAmounts = Array.from(document.querySelectorAll('#transactionsBody .amount-input'))
            .map(input => parseFloat(input.value) || 0);
        
        const totalPayments = paymentAmounts.reduce((sum, amount) => sum + amount, 0);
        const remainingCash = previousBalance - totalPayments;
        
        // Update all displays
        document.getElementById('totalPaymentsFooter').textContent = `PKR ${formatNumber(totalPayments)}`;
        document.getElementById('total_payments').innerHTML = `<i class="fas fa-minus-circle me-1"></i>PKR ${formatNumber(totalPayments)}`;
        document.getElementById('remaining_cash').innerHTML = `<i class="fas fa-calculator me-1"></i>PKR ${formatNumber(remainingCash)}`;
        
        // Color code remaining cash
        const remainingElement = document.getElementById('remaining_cash');
        if (remainingCash >= 0) {
            remainingElement.className = 'form-control-plaintext fw-bold text-success';
        } else {
            remainingElement.className = 'form-control-plaintext fw-bold text-danger';
        }
    }
    
    function formatNumber(num) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const transactionRows = document.querySelectorAll('#transactionsBody tr').length;
        
        if (transactionRows === 0) {
            e.preventDefault();
            alert('Please add at least one transaction.');
            return false;
        }
        
        // Check if all required fields are filled
        let hasErrors = false;
        const allRows = [...document.querySelectorAll('#transactionsBody tr')];
        
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
    });
    
    // Initialize with one transaction row
    addPaymentTransaction();
    updateSummary();
});
</script>
@endpush