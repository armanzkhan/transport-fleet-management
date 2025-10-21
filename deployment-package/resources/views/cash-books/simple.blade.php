@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2 text-success"></i>
                    Simple Cash Book Entry
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('cash-books.store-simple') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="entry_date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('entry_date') is-invalid @enderror" 
                               id="entry_date" name="entry_date" value="{{ old('entry_date', date('Y-m-d', strtotime('+1 day'))) }}" required>
                        @error('entry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('transaction_type') is-invalid @enderror" 
                                id="transaction_type" name="transaction_type" required>
                            <option value="">Select Type</option>
                            <option value="receive" {{ old('transaction_type') == 'receive' ? 'selected' : '' }}>Receive (Money In)</option>
                            <option value="payment" {{ old('transaction_type') == 'payment' ? 'selected' : '' }}>Payment (Money Out)</option>
                        </select>
                        @error('transaction_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3" id="payment_type_div" style="display: none;">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select class="form-select @error('payment_type') is-invalid @enderror" 
                                id="payment_type" name="payment_type">
                            <option value="">Select Payment Type</option>
                            <option value="Advance" {{ old('payment_type') == 'Advance' ? 'selected' : '' }}>Advance</option>
                            <option value="Expense" {{ old('payment_type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                            <option value="Shortage" {{ old('payment_type') == 'Shortage' ? 'selected' : '' }}>Shortage</option>
                        </select>
                        @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account <span class="text-danger">*</span></label>
                        <select class="form-select @error('account_id') is-invalid @enderror" 
                                id="account_id" name="account_id" required>
                            <option value="">Select Account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Vehicle (Optional)</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" 
                                id="vehicle_id" name="vehicle_id">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vrn }} - {{ $vehicle->owner->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3" id="payment_type_div" style="display: none;">
                        <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_type') is-invalid @enderror" 
                                id="payment_type" name="payment_type">
                            <option value="">Select Payment Type</option>
                            <option value="Advance" {{ old('payment_type') == 'Advance' ? 'selected' : '' }}>Advance</option>
                            <option value="Expense" {{ old('payment_type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                            <option value="Shortage" {{ old('payment_type') == 'Shortage' ? 'selected' : '' }}>Shortage</option>
                        </select>
                        @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required 
                                  placeholder="Enter transaction description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount') }}" 
                               step="0.01" min="0.01" placeholder="0.00" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cash-books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transaction_type');
    const paymentTypeDiv = document.getElementById('payment_type_div');
    const paymentType = document.getElementById('payment_type');
    
    // Show/hide payment type based on transaction type
    transactionType.addEventListener('change', function() {
        if (this.value === 'payment') {
            paymentTypeDiv.style.display = 'block';
            paymentType.required = true;
        } else {
            paymentTypeDiv.style.display = 'none';
            paymentType.required = false;
            paymentType.value = '';
        }
    });
    
    // Initialize on page load
    if (transactionType.value === 'payment') {
        paymentTypeDiv.style.display = 'block';
        paymentType.required = true;
    }
});
</script>
@endpush