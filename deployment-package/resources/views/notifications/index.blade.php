@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Notifications</h2>
                <p class="text-muted mb-0">Manage your system notifications and alerts.</p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary me-2" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-2"></i>
                    Mark All as Read
                </button>
                <button type="button" class="btn btn-outline-info" onclick="checkExpiryNotifications()">
                    <i class="fas fa-sync me-2"></i>
                    Check Expiry
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Notifications</option>
                            <option value="unread">Unread Only</option>
                            <option value="read">Read Only</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priorityFilter">
                            <option value="">All Priorities</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="token_tax_expiry">Token Tax Expiry</option>
                            <option value="dip_chart_expiry">Dip Chart Expiry</option>
                            <option value="tracker_expiry">Tracker Expiry</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Priority</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Days Left</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="notificationsTableBody">
                            @forelse($notifications as $notification)
                            <tr class="{{ $notification->is_read ? '' : 'table-warning' }}">
                                <td>
                                    @if($notification->priority === 'high')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($notification->priority === 'medium')
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucwords(str_replace('_', ' ', $notification->type)) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $notification->title }}</strong>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $notification->message }}">
                                        {{ $notification->message }}
                                    </div>
                                </td>
                                <td>
                                    @if($notification->days_left !== null)
                                        <span class="badge bg-{{ $notification->days_left <= 3 ? 'danger' : ($notification->days_left <= 7 ? 'warning' : 'success') }}">
                                            {{ $notification->days_left }} days
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->is_read)
                                        <span class="badge bg-success">Read</span>
                                    @else
                                        <span class="badge bg-warning">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $notification->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if(!$notification->is_read)
                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                onclick="markAsRead({{ $notification->id }})" title="Mark as Read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        
                                        @if($notification->vehicle_id)
                                        <a href="{{ route('vehicles.show', $notification->vehicle_id) }}" 
                                           class="btn btn-outline-info btn-sm" title="View Vehicle">
                                            <i class="fas fa-truck"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                        <h5>No Notifications Found</h5>
                                        <p>You don't have any notifications at the moment.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($notifications->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications
                    </div>
                    <div>
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Mark notification as read
function markAsRead(notificationId) {
    fetch(`{{ route('notifications.mark-read', '') }}/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to mark notification as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking notification as read');
    });
}

// Mark all notifications as read
function markAllAsRead() {
    if (confirm('Are you sure you want to mark all notifications as read?')) {
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to mark all notifications as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking all notifications as read');
        });
    }
}

// Check expiry notifications
function checkExpiryNotifications() {
    fetch('{{ route("notifications.check-expiry") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Expiry notifications checked successfully');
            location.reload();
        } else {
            alert('Failed to check expiry notifications');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error checking expiry notifications');
    });
}

// Filter notifications
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    [statusFilter, priorityFilter, typeFilter].forEach(filter => {
        filter.addEventListener('change', function() {
            // Implement filtering logic here
            console.log('Filter changed:', this.id, this.value);
        });
    });
});
</script>
@endpush
@endsection
