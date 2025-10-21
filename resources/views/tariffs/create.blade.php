@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Add New Tariff
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tariffs.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_date" class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('from_date') is-invalid @enderror" 
                                   id="from_date" name="from_date" value="{{ old('from_date') }}" required>
                            @error('from_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="to_date" class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('to_date') is-invalid @enderror" 
                                   id="to_date" name="to_date" value="{{ old('to_date') }}" required>
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
                                <option value="{{ $carriage->name }}" {{ old('carriage_name') == $carriage->name ? 'selected' : '' }}>
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
                                <option value="{{ $company->name }}" {{ old('company') == $company->name ? 'selected' : '' }}>
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
                                <option value="{{ $point->name }}" {{ old('loading_point') == $point->name ? 'selected' : '' }}>
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
                                @foreach($destinations as $dest)
                                <option value="{{ $dest->name }}" {{ old('destination') == $dest->name ? 'selected' : '' }}>
                                    {{ $dest->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3">Freight Rates</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_freight_rate" class="form-label">Company Freight Rate <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('company_freight_rate') is-invalid @enderror" 
                                   id="company_freight_rate" name="company_freight_rate" 
                                   value="{{ old('company_freight_rate') }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('company_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_freight_rate" class="form-label">Vehicle Freight Rate</label>
                            <input type="number" class="form-control @error('vehicle_freight_rate') is-invalid @enderror" 
                                   id="vehicle_freight_rate" name="vehicle_freight_rate" 
                                   value="{{ old('vehicle_freight_rate') }}" step="0.01" min="0" placeholder="0.00">
                            <div class="form-text">Leave empty to use company rate</div>
                            @error('vehicle_freight_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Shortage Rates</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_shortage_rate" class="form-label">Company Shortage Rate <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('company_shortage_rate') is-invalid @enderror" 
                                   id="company_shortage_rate" name="company_shortage_rate" 
                                   value="{{ old('company_shortage_rate') }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('company_shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_shortage_rate" class="form-label">Vehicle Shortage Rate</label>
                            <input type="number" class="form-control @error('vehicle_shortage_rate') is-invalid @enderror" 
                                   id="vehicle_shortage_rate" name="vehicle_shortage_rate" 
                                   value="{{ old('vehicle_shortage_rate') }}" step="0.01" min="0" placeholder="0.00">
                            <div class="form-text">Leave empty to use company rate</div>
                            @error('vehicle_shortage_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <!-- Go To Navigation Buttons -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('date')">
                                <i class="fas fa-calendar me-1"></i>
                                Go To Date
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('carriage')">
                                <i class="fas fa-truck me-1"></i>
                                Go To Carriage
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('company')">
                                <i class="fas fa-building me-1"></i>
                                Go To Company
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('loading_point')">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Go To Loading Point
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="goToNavigation('destination')">
                                <i class="fas fa-flag me-1"></i>
                                Go To Destination
                            </button>
                        </div>
                        
                        <!-- Search by Tariff Number -->
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control form-control-sm" id="tariffSearch" placeholder="Search by Tariff Number">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="searchByTariff()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('tariffs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Save Tariff
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Search Section -->
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>
                    Quick Search - Recent Tariffs
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Recent Carriages</h6>
                        <div class="list-group list-group-flush">
                            @foreach(\App\Models\Tariff::with('creator')->orderBy('created_at', 'desc')->limit(5)->get() as $recentTariff)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $recentTariff->carriage_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $recentTariff->company }} - {{ $recentTariff->loading_point }} to {{ $recentTariff->destination }}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillFromRecent('{{ $recentTariff->carriage_name }}', '{{ $recentTariff->company }}', '{{ $recentTariff->loading_point }}', '{{ $recentTariff->destination }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Popular Routes</h6>
                        <div class="list-group list-group-flush">
                            @foreach(\App\Models\Tariff::select('loading_point', 'destination')->selectRaw('COUNT(*) as count')->groupBy('loading_point', 'destination')->orderBy('count', 'desc')->limit(5)->get() as $route)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $route->loading_point }} → {{ $route->destination }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $route->count }} tariffs</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="fillRoute('{{ $route->loading_point }}', '{{ $route->destination }}')">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Tariff Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Use Go To buttons to find similar tariffs</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Search by tariff number for quick reference</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Copy from recent tariffs to save time</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Check for duplicate routes before saving</li>
                    <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Set appropriate date ranges</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    
    // Set minimum date for to_date based on from_date
    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
    });
    
    // Set maximum date for from_date based on to_date
    toDateInput.addEventListener('change', function() {
        fromDateInput.max = this.value;
    });
});

// Go To Navigation Functions
function goToNavigation(type) {
    let currentValue = '';
    let searchUrl = '';
    
    switch(type) {
        case 'date':
            currentValue = document.getElementById('from_date').value;
            searchUrl = `{{ route('tariffs.index') }}?search_date=${currentValue}`;
            break;
        case 'carriage':
            currentValue = document.getElementById('carriage_name').value;
            searchUrl = `{{ route('tariffs.index') }}?search_carriage=${encodeURIComponent(currentValue)}`;
            break;
        case 'company':
            currentValue = document.getElementById('company').value;
            searchUrl = `{{ route('tariffs.index') }}?search_company=${encodeURIComponent(currentValue)}`;
            break;
        case 'loading_point':
            currentValue = document.getElementById('loading_point').value;
            searchUrl = `{{ route('tariffs.index') }}?search_loading_point=${encodeURIComponent(currentValue)}`;
            break;
        case 'destination':
            currentValue = document.getElementById('destination').value;
            searchUrl = `{{ route('tariffs.index') }}?search_destination=${encodeURIComponent(currentValue)}`;
            break;
    }
    
    if (currentValue) {
        // Open in new tab to preserve current form
        window.open(searchUrl, '_blank');
    } else {
        alert('Please select a value first to search for similar tariffs.');
    }
}

// Search by Tariff Number Function
function searchByTariff() {
    const tariffNumber = document.getElementById('tariffSearch').value.trim();
    
    if (tariffNumber) {
        const searchUrl = `{{ route('tariffs.index') }}?search_tariff=${encodeURIComponent(tariffNumber)}`;
        // Open in new tab to preserve current form
        window.open(searchUrl, '_blank');
    } else {
        alert('Please enter a tariff number to search.');
    }
}

// Auto-fill tariff number from search
document.getElementById('tariffSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchByTariff();
    }
});

// Fill form from recent tariff
function fillFromRecent(carriage, company, loadingPoint, destination) {
    document.getElementById('carriage_name').value = carriage;
    document.getElementById('company').value = company;
    document.getElementById('loading_point').value = loadingPoint;
    document.getElementById('destination').value = destination;
    
    // Show success message
    showNotification('Form filled from recent tariff!', 'success');
}

// Fill route from popular routes
function fillRoute(loadingPoint, destination) {
    document.getElementById('loading_point').value = loadingPoint;
    document.getElementById('destination').value = destination;
    
    // Show success message
    showNotification('Route filled from popular routes!', 'info');
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}
</script>
@endsection
