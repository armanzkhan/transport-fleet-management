@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2 text-success"></i>
                            Cash Book - Receive Entry
                        </h5>
                        <p class="text-muted mb-0 mt-1">Record cash received transactions</p>
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

                <form method="POST" action="{{ route('cash-books.store-receive') }}" id="receiveForm">
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
                                ₨{{ number_format($todaysPreviousBalance ?? 0, 2) }}
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
                            <label class="form-label">Cash in Hand</label>
                            <div class="form-control-plaintext fw-bold text-info" id="total_cash">
                                <i class="fas fa-calculator me-1"></i>
                                ₨{{ number_format($todaysPreviousBalance ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="card border-success">
                        <div class="card-header bg-success bg-gradient text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-arrow-up me-2"></i>
                                    Cash Receive Transactions
                                </h6>
                                <button type="button" class="btn btn-light btn-sm" id="addTransactionBtn">
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
                                            <th width="25%">Account</th>
                                            <th width="20%">Vehicle</th>
                                            <th width="35%">Description</th>
                                            <th width="15%">Amount</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactionsBody">
                                        <!-- Transactions will be added here -->
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
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('cash-books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Save Receive Entry
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
            <select class="form-select form-select-sm" name="transactions[INDEX][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm" name="transactions[INDEX][vehicle_id]">
                <option value="">Select Vehicle</option>
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
    window.addReceiveTransaction = function() {
        const transactionIndex = document.querySelectorAll('#transactionsBody tr').length;
        const template = document.getElementById('transactionTemplate');
        
        let html;
        
        if (template) {
            // Use template if available
            const clone = template.content.cloneNode(true);
            html = clone.firstElementChild.outerHTML.replace(/INDEX/g, transactionIndex);
        } else {
            // Fallback: create HTML manually
            html = `
                <tr>
                    <td>
                        <select class="form-select form-select-sm" name="transactions[${transactionIndex}][account_id]" required>
                            <option value="">Select Account</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="transactions[${transactionIndex}][vehicle_id]">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vrn }} - {{ $vehicle->owner->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="transactions[${transactionIndex}][description]" placeholder="Description" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm amount-input" name="transactions[${transactionIndex}][amount]" placeholder="0.00" step="0.01" min="0.01" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }
        
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
        
        updateSummary();
    };
    
    function updateSummary() {
        const receiveAmounts = Array.from(document.querySelectorAll('#transactionsBody .amount-input'))
            .map(input => parseFloat(input.value) || 0);
        
        const totalReceives = receiveAmounts.reduce((sum, amount) => sum + amount, 0);
        const totalCashInHand = previousBalance + totalReceives;
        
        // Update all displays
        document.getElementById('totalReceivesFooter').textContent = `PKR ${formatNumber(totalReceives)}`;
        document.getElementById('total_receives').innerHTML = `<i class="fas fa-plus-circle me-1"></i>PKR ${formatNumber(totalReceives)}`;
        document.getElementById('total_cash').innerHTML = `<i class="fas fa-calculator me-1"></i>PKR ${formatNumber(totalCashInHand)}`;
    }
    
    function formatNumber(num) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    // Form validation
    document.getElementById('receiveForm').addEventListener('submit', function(e) {
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
    
    // Add event listener to the button
    document.getElementById('addTransactionBtn').addEventListener('click', function() {
        addReceiveTransaction();
    });
    
    // Initialize with one transaction row
    addReceiveTransaction();
    updateSummary();
});
</script>
@endpush