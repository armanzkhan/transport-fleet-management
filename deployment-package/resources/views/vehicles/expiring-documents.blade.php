@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Expiring Documents</h2>
                <p class="text-muted mb-0">Vehicles with documents expiring within 15 days.</p>
            </div>
            <div>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Vehicles
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4 id="criticalCount">0</h4>
                <p class="mb-0">Critical (≤3 days)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h4 id="warningCount">0</h4>
                <p class="mb-0">Warning (4-7 days)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <h4 id="infoCount">0</h4>
                <p class="mb-0">Info (8-15 days)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body text-center">
                <i class="fas fa-truck fa-2x mb-2"></i>
                <h4 id="totalCount">0</h4>
                <p class="mb-0">Total Vehicles</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <ul class="nav nav-pills nav-fill" id="documentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                            <i class="fas fa-list me-2"></i>All Documents
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tracker-tab" data-bs-toggle="pill" data-bs-target="#tracker" type="button" role="tab">
                            <i class="fas fa-map-marker-alt me-2"></i>GPS Tracker
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="dipchart-tab" data-bs-toggle="pill" data-bs-target="#dipchart" type="button" role="tab">
                            <i class="fas fa-chart-bar me-2"></i>Dip Chart
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tokentax-tab" data-bs-toggle="pill" data-bs-target="#tokentax" type="button" role="tab">
                            <i class="fas fa-file-alt me-2"></i>Token Tax
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content -->
<div class="row">
    <div class="col-12">
        <div class="tab-content" id="documentTabsContent">
            <!-- All Documents -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>
                            All Expiring Documents
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="allDocumentsContent">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GPS Tracker -->
            <div class="tab-pane fade" id="tracker" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            GPS Tracker Expiries
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="trackerContent">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dip Chart -->
            <div class="tab-pane fade" id="dipchart" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Dip Chart Expiries
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="dipChartContent">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Token Tax -->
            <div class="tab-pane fade" id="tokentax" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Token Tax Expiries
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="tokenTaxContent">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notifications data
    fetch('{{ route("notifications.api") }}')
        .then(response => response.json())
        .then(data => {
            updateCounts(data);
            renderAllDocuments(data);
            renderByType(data);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });

    function updateCounts(data) {
        let critical = 0, warning = 0, info = 0;
        
        data.forEach(vehicle => {
            vehicle.expiring_documents.forEach(doc => {
                if (doc.days_remaining <= 3) critical++;
                else if (doc.days_remaining <= 7) warning++;
                else info++;
            });
        });
        
        document.getElementById('criticalCount').textContent = critical;
        document.getElementById('warningCount').textContent = warning;
        document.getElementById('infoCount').textContent = info;
        document.getElementById('totalCount').textContent = data.length;
    }

    function renderAllDocuments(data) {
        const content = document.getElementById('allDocumentsContent');
        
        if (data.length === 0) {
            content.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">All documents are up to date!</h5>
                    <p class="text-muted">No documents are expiring within 15 days.</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        data.forEach(vehicle => {
            vehicle.expiring_documents.forEach(doc => {
                const urgencyClass = doc.days_remaining <= 3 ? 'danger' : 
                                   doc.days_remaining <= 7 ? 'warning' : 'info';
                const iconClass = doc.type === 'Tracker' ? 'map-marker-alt' : 
                                doc.type === 'Dip Chart' ? 'chart-bar' : 'file-alt';
                
                html += `
                    <div class="col-lg-6 mb-3">
                        <div class="card border-${urgencyClass}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 text-primary">${vehicle.vrn}</h6>
                                    <span class="badge bg-${urgencyClass}">
                                        ${doc.days_remaining <= 0 ? 'EXPIRED' : 
                                          doc.days_remaining === 1 ? 'Tomorrow' : 
                                          doc.days_remaining + ' days'}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">Driver: ${vehicle.driver_name}</p>
                                <p class="text-muted small mb-2">Owner: ${vehicle.owner || 'N/A'}</p>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-${iconClass} me-2 text-${urgencyClass}"></i>
                                    <strong>${doc.type}</strong>
                                    <span class="ms-auto text-muted">Expires: ${new Date(doc.expiry_date).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
        html += '</div>';
        
        content.innerHTML = html;
    }

    function renderByType(data) {
        const tracker = data.filter(v => v.expiring_documents.some(d => d.type === 'Tracker'));
        const dipChart = data.filter(v => v.expiring_documents.some(d => d.type === 'Dip Chart'));
        const tokenTax = data.filter(v => v.expiring_documents.some(d => d.type === 'Token Tax'));

        renderDocumentType('trackerContent', tracker, 'Tracker', 'map-marker-alt', 'info');
        renderDocumentType('dipChartContent', dipChart, 'Dip Chart', 'chart-bar', 'warning');
        renderDocumentType('tokenTaxContent', tokenTax, 'Token Tax', 'file-alt', 'danger');
    }

    function renderDocumentType(containerId, vehicles, docType, iconClass, colorClass) {
        const content = document.getElementById(containerId);
        
        if (vehicles.length === 0) {
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h6 class="text-success">All ${docType} documents are up to date!</h6>
                    <p class="text-muted mb-0">No ${docType} documents are expiring within 15 days.</p>
                </div>
            `;
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Vehicle</th><th>Driver</th><th>Owner</th><th>Expiry Date</th><th>Days Remaining</th><th>Status</th></tr></thead><tbody>';
        
        vehicles.forEach(vehicle => {
            const doc = vehicle.expiring_documents.find(d => d.type === docType);
            if (doc) {
                const urgencyClass = doc.days_remaining <= 3 ? 'danger' : 
                                   doc.days_remaining <= 7 ? 'warning' : 'info';
                
                html += `
                    <tr>
                        <td><strong>${vehicle.vrn}</strong></td>
                        <td>${vehicle.driver_name}</td>
                        <td>${vehicle.owner || 'N/A'}</td>
                        <td>${new Date(doc.expiry_date).toLocaleDateString()}</td>
                        <td>
                            <span class="badge bg-${urgencyClass}">
                                ${doc.days_remaining <= 0 ? 'EXPIRED' : 
                                  doc.days_remaining === 1 ? 'Tomorrow' : 
                                  doc.days_remaining + ' days'}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-${iconClass} text-${colorClass}"></i>
                            ${docType}
                        </td>
                    </tr>
                `;
            }
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }
});
</script>
@endpush
