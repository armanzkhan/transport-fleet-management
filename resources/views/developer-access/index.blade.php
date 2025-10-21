@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Developer Access Management</h2>
                <p class="text-muted mb-0">Manage developer access requests and permissions.</p>
            </div>
            <div>
                @can('manage-developer-access')
                <a href="{{ route('developer-access.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Request Access
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-primary bg-gradient">
            <div class="card-body text-white text-center">
                <h4 class="mb-1" id="total-accesses">0</h4>
                <small class="text-white-50">Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-warning bg-gradient">
            <div class="card-body text-white text-center">
                <h4 class="mb-1" id="pending-accesses">0</h4>
                <small class="text-white-50">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-success bg-gradient">
            <div class="card-body text-white text-center">
                <h4 class="mb-1" id="active-accesses">0</h4>
                <small class="text-white-50">Active</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-secondary bg-gradient">
            <div class="card-body text-white text-center">
                <h4 class="mb-1" id="expired-accesses">0</h4>
                <small class="text-white-50">Expired</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-danger bg-gradient">
            <div class="card-body text-white text-center">
                <h4 class="mb-1" id="revoked-accesses">0</h4>
                <small class="text-white-50">Revoked</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-info bg-gradient">
            <div class="card-body text-white text-center">
                <button class="btn btn-light btn-sm" onclick="autoExpireAccesses()">
                    <i class="fas fa-clock me-1"></i>
                    Auto Expire
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('developer-access.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Developer name, email, reason...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="access_type" class="form-label">Access Type</label>
                            <select class="form-select" id="access_type" name="access_type">
                                <option value="">All Types</option>
                                <option value="read_only" {{ request('access_type') == 'read_only' ? 'selected' : '' }}>Read Only</option>
                                <option value="limited_write" {{ request('access_type') == 'limited_write' ? 'selected' : '' }}>Limited Write</option>
                                <option value="full_access" {{ request('access_type') == 'full_access' ? 'selected' : '' }}>Full Access</option>
                                <option value="emergency" {{ request('access_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Access Requests Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Developer</th>
                                <th>Access Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accesses as $access)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $access->developer_name }}</strong>
                                        <br><small class="text-muted">{{ $access->developer_email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $access->access_type === 'emergency' ? 'danger' : ($access->access_type === 'full_access' ? 'warning' : 'info') }}">
                                        {{ $access->access_type_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <small><strong>Start:</strong> {{ $access->start_date->format('M d, Y') }}</small>
                                        <br><small><strong>End:</strong> {{ $access->end_date ? $access->end_date->format('M d, Y') : 'No expiry' }}</small>
                                        <br><small class="text-muted">{{ $access->remaining_time }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $access->status_badge_class }}">{{ ucfirst($access->status) }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $access->reason }}">
                                        {{ $access->reason }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small>{{ $access->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">by {{ $access->creator->name ?? 'System' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($access->status === 'pending')
                                            @can('manage-developer-access')
                                            <form method="POST" action="{{ route('developer-access.approve', $access) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                        
                                        @if($access->status === 'approved')
                                            @can('manage-developer-access')
                                            <form method="POST" action="{{ route('developer-access.activate', $access) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm" title="Activate">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                        
                                        @if(in_array($access->status, ['active', 'approved']))
                                            @can('manage-developer-access')
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    onclick="showRevokeModal({{ $access->id }}, '{{ $access->developer_name }}')" title="Revoke">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            @endcan
                                        @endif
                                        
                                        @can('manage-developer-access')
                                        <form method="POST" action="{{ route('developer-access.destroy', $access) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this access request?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                                        <h5>No Developer Access Requests Found</h5>
                                        <p>Start by creating your first access request.</p>
                                        @can('manage-developer-access')
                                        <a href="{{ route('developer-access.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Request Access
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($accesses->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $accesses->firstItem() ?? 0 }} to {{ $accesses->lastItem() ?? 0 }} of {{ $accesses->total() }} requests
                    </div>
                    <div>
                        {{ $accesses->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Revoke Developer Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revokeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to revoke access for <strong id="revokeDeveloperName"></strong>?</p>
                    <div class="mb-3">
                        <label for="revoke_reason" class="form-label">Reason for revocation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="revoke_reason" name="revoke_reason" rows="3" 
                                  placeholder="Enter the reason for revoking access..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Revoke Access</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    fetch('{{ route("developer-access.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-accesses').textContent = data.total || 0;
            document.getElementById('pending-accesses').textContent = data.pending || 0;
            document.getElementById('active-accesses').textContent = data.active || 0;
            document.getElementById('expired-accesses').textContent = data.expired || 0;
            document.getElementById('revoked-accesses').textContent = data.revoked || 0;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

function showRevokeModal(accessId, developerName) {
    document.getElementById('revokeDeveloperName').textContent = developerName;
    document.getElementById('revokeForm').action = '{{ route("developer-access.revoke", ":id") }}'.replace(':id', accessId);
    const modal = new bootstrap.Modal(document.getElementById('revokeModal'));
    modal.show();
}

function autoExpireAccesses() {
    if (confirm('Are you sure you want to auto-expire all expired accesses?')) {
        fetch('{{ route("developer-access.auto-expire") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadStatistics();
            location.reload();
        })
        .catch(error => {
            console.error('Error auto-expiring accesses:', error);
            alert('Error auto-expiring accesses');
        });
    }
}
</script>
@endpush
@endsection
