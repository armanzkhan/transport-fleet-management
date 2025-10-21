@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Vehicle Billing</h2>
                <p class="text-muted mb-0">Manage vehicle bills and monthly settlements.</p>
            </div>
            <div>
                @can('create-vehicle-billing')
                <a href="{{ route('vehicle-billing.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Create Bill
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Vehicle Bills
                </h5>
            </div>
            <div class="card-body">
                @if($vehicleBills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Bill #</th>
                                <th>Vehicle</th>
                                <th>Owner</th>
                                <th>Billing Month</th>
                                <th>Total Freight</th>
                                <th>Total Advance</th>
                                <th>Total Expense</th>
                                <th>Net Profit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicleBills as $bill)
                            <tr>
                                <td>
                                    <a href="{{ route('vehicle-billing.show', $bill) }}" class="text-decoration-none">
                                        {{ $bill->bill_number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $bill->vehicle->vrn }}
                                    <br><small class="text-muted">{{ $bill->vehicle->driver_name }}</small>
                                </td>
                                <td>{{ $bill->vehicle->owner->name }}</td>
                                <td>{{ $bill->billing_month }}</td>
                                <td>
                                    <strong>₨{{ number_format($bill->total_freight, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="text-warning">₨{{ number_format($bill->total_advance, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-danger">₨{{ number_format($bill->total_expense, 2) }}</span>
                                </td>
                                <td>
                                    <strong class="{{ $bill->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₨{{ number_format($bill->net_profit, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($bill->is_finalized)
                                        <span class="badge bg-success">Finalized</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vehicle-billing.show', $bill) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('print-vehicle-bills')
                                        <a href="{{ route('vehicle-billing.print', $bill) }}" class="btn btn-outline-info btn-sm" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @endcan
                                        @if(!$bill->is_finalized)
                                        @can('finalize-vehicle-billing')
                                        <form action="{{ route('vehicle-billing.finalize', $bill) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" 
                                                    onclick="return confirm('Are you sure you want to finalize this bill?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $vehicleBills->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Vehicle Bills Found</h5>
                    <p class="text-muted">Create your first vehicle bill to get started.</p>
                    @can('create-vehicle-billing')
                    <a href="{{ route('vehicle-billing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create First Bill
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
