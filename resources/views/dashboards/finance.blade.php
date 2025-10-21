@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Finance Dashboard</h2>
                <p class="text-muted mb-0">
                    Financial overview and accounting controls for {{ Auth::user()->name }}.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#financeSettingsModal">
                    <i class="fas fa-cog me-2"></i>
                    Finance Settings
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-2"></i>
                        Export Financial Report
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'csv']) }}">
                            <i class="fas fa-file-csv me-2"></i>
                            Export as CSV
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'html']) }}">
                            <i class="fas fa-file-code me-2"></i>
                            Export as HTML
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.export', ['type' => 'excel']) }}">
                            <i class="fas fa-file-excel me-2"></i>
                            Export as Excel
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Revenue</div>
                        <div class="h4 mb-0">₨{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-credit-card fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Total Expenses</div>
                        <div class="h4 mb-0">₨{{ number_format($stats['total_expenses'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Net Profit</div>
                        <div class="h4 mb-0 text-{{ ($stats['net_profit'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                            ₨{{ number_format($stats['net_profit'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Pending Bills</div>
                        <div class="h4 mb-0">{{ $stats['pending_bills'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Overview -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Cash Flow Overview</h5>
                <p class="text-muted small mb-0">Recent cash book entries and financial transactions.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCashEntries as $entry)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $entry->entry_date ? $entry->entry_date->format('M d, Y') : 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $entry->description }}</div>
                                    <small class="text-muted">{{ $entry->transaction_number }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($entry->transaction_type === 'receive' || $entry->transaction_type === '') ? 'success' : 'danger' }}">
                                        {{ $entry->transaction_type ? ucfirst($entry->transaction_type) : 'Receive' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-{{ ($entry->transaction_type === 'receive' || $entry->transaction_type === '') ? 'success' : 'danger' }}">
                                        {{ ($entry->transaction_type === 'receive' || $entry->transaction_type === '') ? '+' : '-' }}₨{{ number_format($entry->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">₨{{ number_format($entry->total_cash_in_hand, 2) }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No cash entries found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('cash-books.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-book me-1"></i>
                        View All Entries
                    </a>
                    <a href="{{ route('cash-books.receive') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Add Entry
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bill Status Overview -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Bill Status Overview</h5>
                <p class="text-muted small mb-0">Current status of vehicle bills and payments.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bill #</th>
                                <th>Vehicle</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBills as $bill)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $bill->bill_number }}</div>
                                    <small class="text-muted">{{ $bill->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $bill->vehicle ? $bill->vehicle->vrn : 'N/A' }}</div>
                                    <small class="text-muted">{{ $bill->vehicle ? $bill->vehicle->driver_name : 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="fw-medium">₨{{ number_format($bill->total_vehicle_balance ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $bill->is_finalized ? 'success' : 'warning' }}">
                                        {{ $bill->is_finalized ? 'Finalized' : 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $bill->billing_month ? \Carbon\Carbon::parse($bill->billing_month)->format('M d, Y') : 'N/A' }}
                                    </small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                    <div>No bills found</div>
                                    <small>Create your first vehicle bill to get started</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('vehicle-billing.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-invoice me-1"></i>
                        View All Bills
                    </a>
                    <a href="{{ route('vehicle-billing.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Create Bill
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Alerts -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Financial Alerts</h5>
                <p class="text-muted small mb-0">Important financial notifications and warnings.</p>
            </div>
            <div class="card-body p-0">
                @forelse($financialAlerts as $alert)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <i class="fas fa-{{ $alert['icon'] }} text-{{ $alert['type'] }} fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-medium">{{ $alert['title'] }}</div>
                        <div class="text-muted small">{{ $alert['message'] }}</div>
                    </div>
                    <div class="text-muted small">{{ $alert['time'] }}</div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <div>No financial alerts at this time</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Financial Activities -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Financial Activities</h5>
                <p class="text-muted small mb-0">Latest financial transactions and accounting activities.</p>
            </div>
            <div class="card-body p-0">
                @forelse($recentFinancialActivities as $activity)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }} fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-medium">{{ $activity['title'] }}</div>
                        <div class="text-muted small">{{ $activity['description'] }}</div>
                    </div>
                    <div class="text-muted small">{{ $activity['time'] }}</div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    No recent financial activities found.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Finance Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Quick Actions</h5>
                <p class="text-muted small mb-0">Common financial tasks and shortcuts.</p>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('cash-books.receive') }}" class="btn btn-outline-success text-start">
                        <i class="fas fa-plus me-2"></i>
                        Record Receipt
                    </a>
                    
                    <a href="{{ route('cash-books.payment') }}" class="btn btn-outline-danger text-start">
                        <i class="fas fa-minus me-2"></i>
                        Record Payment
                    </a>
                    
                    <a href="{{ route('vehicle-billing.create') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-file-invoice me-2"></i>
                        Create Bill
                    </a>
                    
                    <a href="{{ route('reports.income-reports') }}" class="btn btn-outline-info text-start">
                        <i class="fas fa-chart-bar me-2"></i>
                        Financial Reports
                    </a>
                    
                    <button type="button" class="btn btn-outline-secondary text-start" data-bs-toggle="modal" data-bs-target="#financeSettingsModal">
                        <i class="fas fa-cog me-2"></i>
                        Finance Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Performance Chart -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Financial Performance</h5>
                <p class="text-muted small mb-0">Monthly revenue and expense trends.</p>
            </div>
            <div class="card-body">
                <canvas id="financialPerformanceChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Finance Settings Modal -->
<div class="modal fade" id="financeSettingsModal" tabindex="-1" aria-labelledby="financeSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="financeSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>
                    Finance Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="financeSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Currency Settings</h6>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Default Currency</label>
                                <select class="form-select" id="currency">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="decimalPlaces" class="form-label">Decimal Places</label>
                                <input type="number" class="form-control" id="decimalPlaces" value="2" min="0" max="4">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Notification Settings</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="billReminders" checked>
                                <label class="form-check-label" for="billReminders">
                                    Enable bill payment reminders
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lowBalanceAlerts" checked>
                                <label class="form-check-label" for="lowBalanceAlerts">
                                    Enable low balance alerts
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveFinanceSettings()">
                    <i class="fas fa-save me-1"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Financial Performance Chart
const ctx = document.getElementById('financialPerformanceChart').getContext('2d');
const financialPerformanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Expenses',
            data: [8000, 12000, 10000, 15000, 13000, 18000],
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function saveFinanceSettings() {
    // Implementation for saving finance settings
    alert('Finance settings saved successfully!');
    const modal = bootstrap.Modal.getInstance(document.getElementById('financeSettingsModal'));
    modal.hide();
}
</script>
@endpush
