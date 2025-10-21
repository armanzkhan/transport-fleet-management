@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Reports Dashboard</h2>
                <p class="text-muted mb-0">Access all financial and operational reports.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Financial Reports -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Financial Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.general-ledger') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-book me-2 text-primary"></i>
                                General Ledger
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Complete transaction history and account balances</p>
                    </a>
                    
                    <a href="{{ route('reports.company-trial-balance') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-balance-scale me-2 text-info"></i>
                                Company Trial Balance
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Account balances and trial balance report</p>
                    </a>
                    
                    <a href="{{ route('reports.income-reports') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                Income Reports
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Revenue analysis and income breakdown</p>
                    </a>
                    
                    <a href="{{ route('reports.outstanding-advances') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                Outstanding Advances
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Pending advances and expenses tracking</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Reports -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-truck me-2"></i>
                    Operational Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.company-summary') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-building me-2 text-primary"></i>
                                Company Summary
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Performance summary by company</p>
                    </a>
                    
                    <a href="{{ route('reports.carriage-summary') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-route me-2 text-info"></i>
                                Carriage Summary
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Route-wise performance analysis</p>
                    </a>
                    
                    <a href="{{ route('reports.monthly-vehicle-bills') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>
                                Monthly Vehicle Bills
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Monthly billing summary for all vehicles</p>
                    </a>
                    
                    <a href="{{ route('reports.pending-trips') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-clock me-2 text-danger"></i>
                                Pending Trips
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Unprocessed and pending journey vouchers</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Vehicle Reports -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-database me-2"></i>
                    Vehicle & Database Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.vehicle-database') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-truck me-2 text-primary"></i>
                                Vehicle Database
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Complete vehicle information and status</p>
                    </a>
                    
                    <a href="{{ route('reports.vehicle-owner-ledger') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-users me-2 text-success"></i>
                                Vehicle Owner Ledger
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Owner-wise financial transactions</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Reports -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Special Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.debit-account-notifications') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-bell me-2 text-danger"></i>
                                Debit Account Notifications
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Accounts with negative balances</p>
                    </a>
                    
                    <a href="{{ route('reports.unattached-shortages') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                Unattached Shortages
                            </h6>
                        </div>
                        <p class="mb-1 text-muted">Shortages not linked to specific journeys</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Quick Report Access
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('reports.general-ledger') }}" class="btn btn-outline-primary">
                                <i class="fas fa-book me-2"></i>
                                General Ledger
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('reports.company-summary') }}" class="btn btn-outline-success">
                                <i class="fas fa-building me-2"></i>
                                Company Summary
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('reports.pending-trips') }}" class="btn btn-outline-warning">
                                <i class="fas fa-clock me-2"></i>
                                Pending Trips
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('reports.vehicle-database') }}" class="btn btn-outline-info">
                                <i class="fas fa-truck me-2"></i>
                                Vehicle Database
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
