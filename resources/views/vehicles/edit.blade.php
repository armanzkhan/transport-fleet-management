@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        Edit Vehicle: {{ $vehicle->vrn }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i>
                            View Details
                        </a>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Vehicles
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vrn" class="form-label">Vehicle Registration Number (VRN) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('vrn') is-invalid @enderror" 
                                   id="vrn" name="vrn" value="{{ old('vrn', $vehicle->vrn) }}" required>
                            @error('vrn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="owner_id" class="form-label">Vehicle Owner <span class="text-danger">*</span></label>
                            <select class="form-select @error('owner_id') is-invalid @enderror" 
                                    id="owner_id" name="owner_id" required>
                                <option value="">Select Owner</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('owner_id', $vehicle->owner_id) == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('owner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity (Liters) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" 
                                   step="0.01" min="0" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select @error('is_active') is-invalid @enderror" 
                                    id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $vehicle->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $vehicle->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Driver Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-user me-1"></i>
                                Driver Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="driver_name" class="form-label">Driver Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('driver_name') is-invalid @enderror" 
                                   id="driver_name" name="driver_name" value="{{ old('driver_name', $vehicle->driver_name) }}" required>
                            @error('driver_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="driver_contact" class="form-label">Driver Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('driver_contact') is-invalid @enderror" 
                                   id="driver_contact" name="driver_contact" value="{{ old('driver_contact', $vehicle->driver_contact) }}" required>
                            @error('driver_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Vehicle Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-cog me-1"></i>
                                Vehicle Details
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="engine_number" class="form-label">Engine Number</label>
                            <input type="text" class="form-control @error('engine_number') is-invalid @enderror" 
                                   id="engine_number" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}">
                            @error('engine_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="chassis_number" class="form-label">Chassis Number</label>
                            <input type="text" class="form-control @error('chassis_number') is-invalid @enderror" 
                                   id="chassis_number" name="chassis_number" value="{{ old('chassis_number', $vehicle->chassis_number) }}">
                            @error('chassis_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="induction_date" class="form-label">Induction Date</label>
                            <input type="date" class="form-control @error('induction_date') is-invalid @enderror" 
                                   id="induction_date" name="induction_date" 
                                   value="{{ old('induction_date', $vehicle->induction_date?->format('Y-m-d')) }}">
                            @error('induction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Document Expiries -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-file-alt me-1"></i>
                                Document Expiries
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="token_tax_expiry" class="form-label">Token Tax Expiry</label>
                            <input type="date" class="form-control @error('token_tax_expiry') is-invalid @enderror" 
                                   id="token_tax_expiry" name="token_tax_expiry" 
                                   value="{{ old('token_tax_expiry', $vehicle->token_tax_expiry?->format('Y-m-d')) }}">
                            @error('token_tax_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="dip_chart_expiry" class="form-label">Dip Chart Expiry</label>
                            <input type="date" class="form-control @error('dip_chart_expiry') is-invalid @enderror" 
                                   id="dip_chart_expiry" name="dip_chart_expiry" 
                                   value="{{ old('dip_chart_expiry', $vehicle->dip_chart_expiry?->format('Y-m-d')) }}">
                            @error('dip_chart_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Tracker Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Tracker Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tracker_name" class="form-label">Tracker Name</label>
                            <input type="text" class="form-control @error('tracker_name') is-invalid @enderror" 
                                   id="tracker_name" name="tracker_name" value="{{ old('tracker_name', $vehicle->tracker_name) }}">
                            @error('tracker_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tracker_link" class="form-label">Tracker Link</label>
                            <input type="url" class="form-control @error('tracker_link') is-invalid @enderror" 
                                   id="tracker_link" name="tracker_link" value="{{ old('tracker_link', $vehicle->tracker_link) }}" 
                                   placeholder="https://example.com">
                            @error('tracker_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tracker_id" class="form-label">Tracker ID</label>
                            <input type="text" class="form-control @error('tracker_id') is-invalid @enderror" 
                                   id="tracker_id" name="tracker_id" value="{{ old('tracker_id', $vehicle->tracker_id) }}">
                            @error('tracker_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tracker_password" class="form-label">Tracker Password</label>
                            <input type="text" class="form-control @error('tracker_password') is-invalid @enderror" 
                                   id="tracker_password" name="tracker_password" value="{{ old('tracker_password', $vehicle->tracker_password) }}">
                            @error('tracker_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tracker_expiry" class="form-label">Tracker Expiry</label>
                            <input type="date" class="form-control @error('tracker_expiry') is-invalid @enderror" 
                                   id="tracker_expiry" name="tracker_expiry" 
                                   value="{{ old('tracker_expiry', $vehicle->tracker_expiry?->format('Y-m-d')) }}">
                            @error('tracker_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection