@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit Cash Book Entry
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cash-books.update', $cashBook) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="account_id" class="form-label">Account <span class="text-danger">*</span></label>
                            <select class="form-select @error('account_id') is-invalid @enderror" 
                                    id="account_id" name="account_id" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" 
                                        {{ old('account_id', $cashBook->account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_id" class="form-label">Vehicle</label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" 
                                    id="vehicle_id" name="vehicle_id">
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" 
                                        {{ old('vehicle_id', $cashBook->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vrn }} - {{ $vehicle->owner->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    @if($cashBook->transaction_type == 'payment')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <select class="form-select @error('payment_type') is-invalid @enderror" 
                                    id="payment_type" name="payment_type">
                                <option value="">Select Type</option>
                                <option value="Advance" {{ old('payment_type', $cashBook->payment_type) == 'Advance' ? 'selected' : '' }}>Advance</option>
                                <option value="Expense" {{ old('payment_type', $cashBook->payment_type) == 'Expense' ? 'selected' : '' }}>Expense</option>
                                <option value="Shortage" {{ old('payment_type', $cashBook->payment_type) == 'Shortage' ? 'selected' : '' }}>Shortage</option>
                            </select>
                            @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Enter transaction description" required>{{ old('description', $cashBook->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $cashBook->amount) }}" 
                                   step="0.01" min="0.01" placeholder="0.00" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cash-books.show', $cashBook) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
