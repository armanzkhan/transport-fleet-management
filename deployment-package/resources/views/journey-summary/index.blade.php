@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Journey Summary</h2>
                <p class="text-muted mb-0">Process journey vouchers for billing and PR04 entries.</p>
            </div>
            <div>
                <a href="{{ route('journey-vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Journey Vouchers
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list-check me-2"></i>
                    Journey Vouchers Ready for Processing
                </h5>
            </div>
            <div class="card-body">
                @if($journeyVouchers->count() > 0)
                <form id="journeySummaryForm" action="{{ route('journey-summary.process') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Select All</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info" id="selectedCount">0 selected</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox">
                                    </th>
                                    <th>Journey #</th>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>Route</th>
                                    <th>Company</th>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journeyVouchers as $voucher)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="journey-checkbox" name="journey_ids[]" value="{{ $voucher->id }}">
                                    </td>
                                    <td>
                                        <a href="{{ route('journey-vouchers.show', $voucher) }}" class="text-decoration-none">
                                            {{ $voucher->journey_number }}
                                        </a>
                                    </td>
                                    <td>{{ $voucher->journey_date->format('d M Y') }}</td>
                                    <td>
                                        {{ $voucher->vehicle->vrn }}
                                        <br><small class="text-muted">{{ $voucher->vehicle->driver_name }}</small>
                                    </td>
                                    <td>
                                        {{ $voucher->loading_point }} → {{ $voucher->destination }}
                                        <br><small class="text-muted">{{ $voucher->product }}</small>
                                    </td>
                                    <td>{{ $voucher->company }}</td>
                                    <td>
                                        @if($voucher->invoice_number)
                                            <span class="badge bg-success">{{ $voucher->invoice_number }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>₨{{ number_format($voucher->total_amount, 2) }}</strong>
                                        <br><small class="text-muted">Freight: ₨{{ number_format($voucher->freight_amount, 2) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $voucher->journey_type === 'primary' ? 'primary' : 'success' }}">
                                            {{ ucfirst($voucher->journey_type) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Summary</h6>
                                    <div id="summaryInfo">
                                        <p class="text-muted mb-0">Select journeys to see summary</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Processing Actions</h6>
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="action" value="add_to_billing" class="btn btn-success" disabled id="billingBtn">
                                            <i class="fas fa-file-invoice-dollar me-2"></i>
                                            Add to Billing
                                        </button>
                                        <button type="submit" name="action" value="add_to_pr04" class="btn btn-info" disabled id="pr04Btn">
                                            <i class="fas fa-file-alt me-2"></i>
                                            Add to PR04
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Journey Vouchers Ready for Processing</h5>
                    <p class="text-muted">Journey vouchers with invoice numbers will appear here for processing.</p>
                    <a href="{{ route('journey-vouchers.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create Journey Voucher
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($journeyVouchers->count() > 0)
<!-- Unprocess Form -->
<form id="unprocessForm" action="{{ route('journey-summary.unprocess') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="journey_ids" id="unprocessJourneyIds">
</form>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllLabel = document.getElementById('selectAll');
    const journeyCheckboxes = document.querySelectorAll('.journey-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const summaryInfo = document.getElementById('summaryInfo');
    const billingBtn = document.getElementById('billingBtn');
    const pr04Btn = document.getElementById('pr04Btn');
    const unprocessForm = document.getElementById('unprocessForm');
    const unprocessJourneyIds = document.getElementById('unprocessJourneyIds');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        journeyCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    selectAllLabel.addEventListener('click', function() {
        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        selectAllCheckbox.dispatchEvent(new Event('change'));
    });

    // Individual checkbox change
    journeyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });

    function updateSelection() {
        const checkedBoxes = document.querySelectorAll('.journey-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = `${count} selected`;
        selectedCount.className = count > 0 ? 'badge bg-success' : 'badge bg-info';
        
        // Enable/disable buttons
        billingBtn.disabled = count === 0;
        pr04Btn.disabled = count === 0;
        
        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === journeyCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
        
        // Update summary
        updateSummary(checkedBoxes);
    }

    function updateSummary(checkedBoxes) {
        if (checkedBoxes.length === 0) {
            summaryInfo.innerHTML = '<p class="text-muted mb-0">Select journeys to see summary</p>';
            return;
        }

        const journeyIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Fetch summary data
        fetch('{{ route("journey-summary.data") }}?journey_ids=' + journeyIds.join(','))
            .then(response => response.json())
            .then(data => {
                if (data.summary) {
                    const summary = data.summary;
                    summaryInfo.innerHTML = `
                        <div class="row">
                            <div class="col-6"><strong>Journeys:</strong></div>
                            <div class="col-6">${summary.journey_count}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Total Freight:</strong></div>
                            <div class="col-6">₹${summary.total_freight.toFixed(2)}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Total Shortage:</strong></div>
                            <div class="col-6">₹${summary.total_shortage.toFixed(2)}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Total Commission:</strong></div>
                            <div class="col-6">₹${summary.total_commission.toFixed(2)}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><strong>Total Amount:</strong></div>
                            <div class="col-6"><strong>₹${summary.total_amount.toFixed(2)}</strong></div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching summary:', error);
                summaryInfo.innerHTML = '<p class="text-danger mb-0">Error loading summary</p>';
            });
    }

    // Form submission confirmation
    document.getElementById('journeySummaryForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.journey-checkbox:checked');
        const action = e.submitter.value;
        const actionText = action === 'add_to_billing' ? 'add to billing' : 'add to PR04';
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one journey voucher.');
            return;
        }
        
        if (!confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} journey voucher(s)?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
