@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Request Developer Access</h2>
                <p class="text-muted mb-0">Submit a request for developer access to the system.</p>
            </div>
            <div>
                <a href="{{ route('developer-access.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('developer-access.store') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        Developer Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="developer_name" class="form-label">Developer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="developer_name" name="developer_name" 
                                   value="{{ old('developer_name') }}" placeholder="Enter developer name" required>
                            @error('developer_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="developer_email" class="form-label">Developer Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="developer_email" name="developer_email" 
                                   value="{{ old('developer_email') }}" placeholder="developer@example.com" required>
                            @error('developer_email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="access_type" class="form-label">Access Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="access_type" name="access_type" required>
                                <option value="">Select Access Type</option>
                                <option value="read_only" {{ old('access_type') == 'read_only' ? 'selected' : '' }}>Read Only</option>
                                <option value="limited_write" {{ old('access_type') == 'limited_write' ? 'selected' : '' }}>Limited Write</option>
                                <option value="full_access" {{ old('access_type') == 'full_access' ? 'selected' : '' }}>Full Access</option>
                                <option value="emergency" {{ old('access_type') == 'emergency' ? 'selected' : '' }}>Emergency Access</option>
                            </select>
                            @error('access_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                   value="{{ old('start_date', date('Y-m-d\TH:i')) }}" required>
                            @error('start_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                   value="{{ old('end_date') }}">
                            <div class="form-text">Leave empty for no expiry</div>
                            @error('end_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration</label>
                            <div class="form-control-plaintext" id="duration-display">
                                Select start and end dates to see duration
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Access <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" 
                                  placeholder="Please provide a detailed reason for requesting developer access..." required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Access Types
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Read Only</h6>
                        <small class="text-muted">View data only, no modifications allowed</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-warning">Limited Write</h6>
                        <small class="text-muted">Limited write access to specific modules</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-danger">Full Access</h6>
                        <small class="text-muted">Complete system access with all permissions</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-dark">Emergency</h6>
                        <small class="text-muted">Emergency access for critical situations</small>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li>• All access requests require approval</li>
                        <li>• Access will be monitored and logged</li>
                        <li>• Emergency access has additional restrictions</li>
                        <li>• Access can be revoked at any time</li>
                        <li>• All activities are audited</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('developer-access.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit Request
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Calculate duration
function calculateDuration() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 0) {
            document.getElementById('duration-display').textContent = `${diffDays} days`;
        } else {
            document.getElementById('duration-display').textContent = 'Invalid duration';
        }
    } else if (startDate) {
        document.getElementById('duration-display').textContent = 'No end date set';
    } else {
        document.getElementById('duration-display').textContent = 'Select start and end dates to see duration';
    }
}

// Add event listeners
document.getElementById('start_date').addEventListener('change', calculateDuration);
document.getElementById('end_date').addEventListener('change', calculateDuration);

// Initialize duration calculation
document.addEventListener('DOMContentLoaded', function() {
    calculateDuration();
});
</script>
@endpush
@endsection
