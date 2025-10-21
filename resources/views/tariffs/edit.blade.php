@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Edit Tariff</h2>
                <p class="text-muted mb-0">Route: {{ $tariff->loading_point }} → {{ $tariff->destination }}</p>
            </div>
            <div>
                <a href="{{ route('tariffs.show', $tariff) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-eye me-2"></i>
                    View Details
                </a>
                <a href="{{ route('tariffs.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-edit me-2"></i>
                    Edit Tariff Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tariffs.update', $tariff) }}" method="POST" id="editTariffForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_date" class="form-label">Valid From <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('from_date') is-invalid @enderror" 
                                   id="from_date" name="from_date" value="{{ old('from_date', $tariff->from_date->format('Y-m-d')) }}" required>
                            @error('from_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="to_date" class="form-label">Valid To <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('to_date') is-invalid @enderror" 
                                   id="to_date" name="to_date" value="{{ old('to_date', $tariff->to_date->format('Y-m-d')) }}" required>
                            @error('to_date')
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
                                    <option value="{{ $carriage->name }}" {{ old('carriage_name', $tariff->carriage_name) == $carriage->name ? 'selected' : '' }}>
                                        {{ $carriage->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('carriage_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                            <select class="form-select @error('company') is-invalid @enderror" 
                                    id="company" name="company" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->name }}" {{ old('company', $tariff->company) == $company->name ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="loading_point" class="form-label">Loading Point <span class="text-danger">*</span></label>
                            <select class="form-select @error('loading_point') is-invalid @enderror" 
                                    id="loading_point" name="loading_point" required>
                                <option value="">Select Loading Point</option>
                                @foreach($loadingPoints as $point)
                                    <option value="{{ $point->name }}" {{ old('loading_point', $tariff->loading_point) == $point->name ? 'selected' : '' }}>
                                        {{ $point->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('loading_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('destination') is-invalid @enderror" 
                                    id="destination" name="destination" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->name }}" {{ old('destination', $tariff->destination) == $destination->name ? 'selected' : '' }}>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_freight_rate" class="form-label">Company Freight Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('company_freight_rate') is-invalid @enderror" 
                                   id="company_freight_rate" name="company_freight_rate" value="{{ old('company_freight_rate', $tariff->company_freight_rate) }}" required>
                            @error('company_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_freight_rate" class="form-label">Vehicle Freight Rate</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_freight_rate') is-invalid @enderror" 
                                   id="vehicle_freight_rate" name="vehicle_freight_rate" value="{{ old('vehicle_freight_rate', $tariff->vehicle_freight_rate) }}">
                            @error('vehicle_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_shortage_rate" class="form-label">Company Shortage Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('company_shortage_rate') is-invalid @enderror" 
                                   id="company_shortage_rate" name="company_shortage_rate" value="{{ old('company_shortage_rate', $tariff->company_shortage_rate) }}" required>
                            @error('company_shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_shortage_rate" class="form-label">Vehicle Shortage Rate</label>
                            <input type="number" step="0.01" class="form-control @error('vehicle_shortage_rate') is-invalid @enderror" 
                                   id="vehicle_shortage_rate" name="vehicle_shortage_rate" value="{{ old('vehicle_shortage_rate', $tariff->vehicle_shortage_rate) }}">
                            @error('vehicle_shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tariffs.show', $tariff) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Tariff
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
                    Rate Preview
                </h5>
            </div>
            <div class="card-body">
                <div id="ratePreview">
                    <p class="text-muted">Fill in the form to see rate calculations</p>
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
                    <div class="col-6"><strong>Company Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_freight_rate, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Vehicle Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->vehicle_freight_rate ?: $tariff->company_freight_rate, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Company Shortage:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_shortage_rate, 2) }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Vehicle Shortage:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->vehicle_shortage_rate ?: $tariff->company_shortage_rate, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Tariff History
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Last Updated:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Created By:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->creator->name }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Valid Period:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->from_date->diffInDays($tariff->to_date) + 1 }} days</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editTariffForm');
    const preview = document.getElementById('ratePreview');

    // Calculate preview when form values change
    form.addEventListener('input', calculatePreview);

    // Initial calculation
    calculatePreview();

    function calculatePreview() {
        const companyRate = parseFloat(document.getElementById('company_freight_rate').value) || 0;
        const vehicleRate = parseFloat(document.getElementById('vehicle_freight_rate').value) || companyRate;
        const companyShortage = parseFloat(document.getElementById('company_shortage_rate').value) || 0;
        const vehicleShortage = parseFloat(document.getElementById('vehicle_shortage_rate').value) || companyShortage;

        if (companyRate > 0) {
            const freightDifference = companyRate - vehicleRate;
            const shortageDifference = companyShortage - vehicleShortage;

            preview.innerHTML = `
                <div class="row mb-2">
                    <div class="col-6"><strong>Company Rate:</strong></div>
                    <div class="col-6">₹${companyRate.toFixed(2)}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Vehicle Rate:</strong></div>
                    <div class="col-6">₹${vehicleRate.toFixed(2)}</div>
                </div>
                ${freightDifference !== 0 ? `
                <div class="row mb-2">
                    <div class="col-6"><strong>Rate Difference:</strong></div>
                    <div class="col-6 ${freightDifference > 0 ? 'text-success' : 'text-danger'}">₹${freightDifference.toFixed(2)}</div>
                </div>
                ` : ''}
                <hr>
                <div class="row mb-2">
                    <div class="col-6"><strong>Company Shortage:</strong></div>
                    <div class="col-6">₹${companyShortage.toFixed(2)}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Vehicle Shortage:</strong></div>
                    <div class="col-6">₹${vehicleShortage.toFixed(2)}</div>
                </div>
                ${shortageDifference !== 0 ? `
                <div class="row mb-2">
                    <div class="col-6"><strong>Shortage Difference:</strong></div>
                    <div class="col-6 ${shortageDifference > 0 ? 'text-success' : 'text-danger'}">₹${shortageDifference.toFixed(2)}</div>
                </div>
                ` : ''}
            `;
        } else {
            preview.innerHTML = '<p class="text-muted">Fill in the rates to see calculations</p>';
        }
    }
});
</script>
@endpush
