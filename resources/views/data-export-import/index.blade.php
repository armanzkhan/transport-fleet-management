@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Data Export & Import</h2>
                    <p class="text-muted mb-0">Export and import your fleet management data in various formats.</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="refreshStatistics()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Statistics
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statisticsCards">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                    <h5 class="mb-1" id="vehiclesCount">-</h5>
                    <small class="text-muted">Vehicles</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h5 class="mb-1" id="vehicleOwnersCount">-</h5>
                    <small class="text-muted">Owners</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-route fa-2x text-warning mb-2"></i>
                    <h5 class="mb-1" id="journeyVouchersCount">-</h5>
                    <small class="text-muted">Journeys</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice fa-2x text-info mb-2"></i>
                    <h5 class="mb-1" id="vehicleBillsCount">-</h5>
                    <small class="text-muted">Bills</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-2x text-primary mb-2"></i>
                    <h5 class="mb-1" id="cashBookEntriesCount">-</h5>
                    <small class="text-muted">Cash Entries</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-2x text-secondary mb-2"></i>
                    <h5 class="mb-1" id="auditLogsCount">-</h5>
                    <small class="text-muted">Audit Logs</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Export Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>Export Data
                    </h5>
                </div>
                <div class="card-body">
                    <form id="exportForm">
                        @csrf
                        <div class="mb-3">
                            <label for="exportDataType" class="form-label">Data Type</label>
                            <select class="form-select" id="exportDataType" name="data_type" required>
                                <option value="">Select data type to export</option>
                                <option value="vehicles">Vehicles</option>
                                <option value="vehicle_owners">Vehicle Owners</option>
                                <option value="journey_vouchers">Journey Vouchers</option>
                                <option value="vehicle_bills">Vehicle Bills</option>
                                <option value="cash_book">Cash Book Entries</option>
                                <option value="audit_logs">Audit Logs</option>
                                <option value="dashboard_summary">Dashboard Summary</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="exportFormat" class="form-label">Export Format</label>
                            <select class="form-select" id="exportFormat" name="format" required>
                                <option value="csv">CSV (Excel Compatible)</option>
                                <option value="excel">Excel</option>
                                <option value="json">JSON</option>
                                <option value="pdf">PDF/HTML</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="exportBtn">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Bulk Export</h6>
                    <form id="bulkExportForm">
                        @csrf
                        <div class="mb-3">
                            <label for="bulkExportFormat" class="form-label">Format</label>
                            <select class="form-select" id="bulkExportFormat" name="format" required>
                                <option value="json">JSON (Complete Data)</option>
                                <option value="csv">CSV (Summary)</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary" id="bulkExportBtn">
                                <i class="fas fa-database me-2"></i>Export All Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>Import Data
                    </h5>
                </div>
                <div class="card-body">
                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="importDataType" class="form-label">Data Type</label>
                            <select class="form-select" id="importDataType" name="data_type" required>
                                <option value="">Select data type to import</option>
                                <option value="vehicles">Vehicles</option>
                                <option value="vehicle_owners">Vehicle Owners</option>
                                <option value="journey_vouchers">Journey Vouchers</option>
                                <option value="cash_book">Cash Book Entries</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="importFile" class="form-label">CSV File</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.txt" required>
                            <div class="form-text">Maximum file size: 10MB</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" id="importBtn">
                                <i class="fas fa-upload me-2"></i>Import Data
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Import Templates</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success btn-sm" onclick="downloadTemplate('vehicles')">
                            <i class="fas fa-file-csv me-2"></i>Vehicles Template
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="downloadTemplate('vehicle_owners')">
                            <i class="fas fa-file-csv me-2"></i>Owners Template
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="downloadTemplate('journey_vouchers')">
                            <i class="fas fa-file-csv me-2"></i>Journeys Template
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="downloadTemplate('cash_book')">
                            <i class="fas fa-file-csv me-2"></i>Cash Book Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row" id="resultsSection" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Operation Results
                    </h5>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Results will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.2s ease-in-out;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

#resultsSection {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load initial statistics
    refreshStatistics();
    
    // Export form handler
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        exportData();
    });
    
    // Bulk export form handler
    document.getElementById('bulkExportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        bulkExport();
    });
    
    // Import form handler
    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();
        importData();
    });
});

// Refresh statistics
function refreshStatistics() {
    fetch('{{ route("data-export-import.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('vehiclesCount').textContent = data.vehicles || 0;
            document.getElementById('vehicleOwnersCount').textContent = data.vehicle_owners || 0;
            document.getElementById('journeyVouchersCount').textContent = data.journey_vouchers || 0;
            document.getElementById('vehicleBillsCount').textContent = data.vehicle_bills || 0;
            document.getElementById('cashBookEntriesCount').textContent = data.cash_book_entries || 0;
            document.getElementById('auditLogsCount').textContent = data.audit_logs || 0;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            showAlert('Failed to load statistics', 'danger');
        });
}

// Export data
function exportData() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const exportBtn = document.getElementById('exportBtn');
    
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
    exportBtn.disabled = true;
    
    fetch('{{ route("data-export-import.export") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        } else {
            return response.json().then(data => Promise.reject(data));
        }
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'export_' + new Date().getTime() + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showAlert('Data exported successfully!', 'success');
    })
    .catch(error => {
        console.error('Export error:', error);
        showAlert('Export failed: ' + (error.error || 'Unknown error'), 'danger');
    })
    .finally(() => {
        exportBtn.innerHTML = '<i class="fas fa-download me-2"></i>Export Data';
        exportBtn.disabled = false;
    });
}

// Bulk export
function bulkExport() {
    const form = document.getElementById('bulkExportForm');
    const formData = new FormData(form);
    const bulkExportBtn = document.getElementById('bulkExportBtn');
    
    bulkExportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
    bulkExportBtn.disabled = true;
    
    fetch('{{ route("data-export-import.bulk-export") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        } else {
            return response.json().then(data => Promise.reject(data));
        }
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'bulk_export_' + new Date().getTime() + '.json';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showAlert('Bulk export completed successfully!', 'success');
    })
    .catch(error => {
        console.error('Bulk export error:', error);
        showAlert('Bulk export failed: ' + (error.error || 'Unknown error'), 'danger');
    })
    .finally(() => {
        bulkExportBtn.innerHTML = '<i class="fas fa-database me-2"></i>Export All Data';
        bulkExportBtn.disabled = false;
    });
}

// Import data
function importData() {
    const form = document.getElementById('importForm');
    const formData = new FormData(form);
    const importBtn = document.getElementById('importBtn');
    
    importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importing...';
    importBtn.disabled = true;
    
    fetch('{{ route("data-export-import.import") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResults(data.results);
            showAlert('Import completed successfully!', 'success');
            refreshStatistics();
        } else {
            showAlert('Import failed: ' + (data.error || 'Unknown error'), 'danger');
        }
    })
    .catch(error => {
        console.error('Import error:', error);
        showAlert('Import failed: ' + (error.error || 'Unknown error'), 'danger');
    })
    .finally(() => {
        importBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Import Data';
        importBtn.disabled = false;
        form.reset();
    });
}

// Download template
function downloadTemplate(dataType) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("data-export-import.download-template") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const dataTypeInput = document.createElement('input');
    dataTypeInput.type = 'hidden';
    dataTypeInput.name = 'data_type';
    dataTypeInput.value = dataType;
    
    form.appendChild(csrfToken);
    form.appendChild(dataTypeInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Show results
function showResults(results) {
    const resultsSection = document.getElementById('resultsSection');
    const resultsContent = document.getElementById('resultsContent');
    
    let html = '<div class="row">';
    html += '<div class="col-md-4"><div class="alert alert-success"><strong>Success:</strong> ' + results.success + ' records</div></div>';
    html += '<div class="col-md-4"><div class="alert alert-warning"><strong>Skipped:</strong> ' + results.skipped + ' records</div></div>';
    html += '<div class="col-md-4"><div class="alert alert-danger"><strong>Errors:</strong> ' + results.errors.length + ' records</div></div>';
    html += '</div>';
    
    if (results.errors.length > 0) {
        html += '<h6>Error Details:</h6><ul class="list-group">';
        results.errors.forEach(error => {
            html += '<li class="list-group-item text-danger">' + error + '</li>';
        });
        html += '</ul>';
    }
    
    resultsContent.innerHTML = html;
    resultsSection.style.display = 'block';
}

// Show alert
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>
@endpush
