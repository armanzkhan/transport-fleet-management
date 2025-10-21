@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Tariff Details</h2>
                <p class="text-muted mb-0">Route: {{ $tariff->loading_point }} → {{ $tariff->destination }}</p>
            </div>
            <div>
                <a href="{{ route('tariffs.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
                @can('edit-tariffs')
                <a href="{{ route('tariffs.edit', $tariff) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-route me-2"></i>
                    Tariff Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Valid From</label>
                        <p class="form-control-plaintext">{{ $tariff->from_date->format('d M Y') }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Valid To</label>
                        <p class="form-control-plaintext">{{ $tariff->to_date->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Carriage Name</label>
                        <p class="form-control-plaintext">{{ $tariff->carriage_name }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company</label>
                        <p class="form-control-plaintext">{{ $tariff->company }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Loading Point</label>
                        <p class="form-control-plaintext">{{ $tariff->loading_point }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Destination</label>
                        <p class="form-control-plaintext">{{ $tariff->destination }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            @if($tariff->from_date <= now() && $tariff->to_date >= now())
                                <span class="badge bg-success">Active</span>
                            @elseif($tariff->from_date > now())
                                <span class="badge bg-info">Future</span>
                            @else
                                <span class="badge bg-secondary">Expired</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Created By</label>
                        <p class="form-control-plaintext">{{ $tariff->creator->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Freight Rates
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><strong>Company Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_freight_rate, 2) }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6"><strong>Vehicle Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->vehicle_freight_rate ?: $tariff->company_freight_rate, 2) }}</div>
                </div>
                
                @if($tariff->vehicle_freight_rate && $tariff->vehicle_freight_rate != $tariff->company_freight_rate)
                <div class="row mb-3">
                    <div class="col-6"><strong>Rate Difference:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_freight_rate - $tariff->vehicle_freight_rate, 2) }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Shortage Rates
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><strong>Company Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_shortage_rate, 2) }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6"><strong>Vehicle Rate:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->vehicle_shortage_rate ?: $tariff->company_shortage_rate, 2) }}</div>
                </div>
                
                @if($tariff->vehicle_shortage_rate && $tariff->vehicle_shortage_rate != $tariff->company_shortage_rate)
                <div class="row mb-3">
                    <div class="col-6"><strong>Rate Difference:</strong></div>
                    <div class="col-6 text-end">₨{{ number_format($tariff->company_shortage_rate - $tariff->vehicle_shortage_rate, 2) }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Tariff Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Updated:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Duration:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->from_date->diffInDays($tariff->to_date) + 1 }} days</div>
                </div>
                
                @if($tariff->to_date < now())
                <div class="row mb-2">
                    <div class="col-6"><strong>Expired:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->to_date->diffForHumans() }}</div>
                </div>
                @elseif($tariff->from_date > now())
                <div class="row mb-2">
                    <div class="col-6"><strong>Starts:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->from_date->diffForHumans() }}</div>
                </div>
                @else
                <div class="row mb-2">
                    <div class="col-6"><strong>Expires:</strong></div>
                    <div class="col-6 text-end">{{ $tariff->to_date->diffForHumans() }}</div>
                </div>
                @endif
            </div>
        </div>

        @can('delete-tariffs')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash me-2"></i>
                    Danger Zone
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Once deleted, this tariff cannot be recovered.</p>
                <form action="{{ route('tariffs.destroy', $tariff) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this tariff?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash me-2"></i>
                        Delete Tariff
                    </button>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
