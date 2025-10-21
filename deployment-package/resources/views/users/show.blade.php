@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">User Details</h2>
                <p class="text-muted mb-0">View user information and permissions.</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Users
                </a>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit User
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <div class="fw-medium">{{ $user->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Email Address</label>
                            <div class="fw-medium">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role</label>
                            <div>
                                <span class="badge bg-{{ $user->roles->first() ? 'primary' : 'secondary' }}">
                                    {{ $user->roles->first() ? $user->roles->first()->name : 'No Role' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <div class="fw-medium">{{ $user->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Last Login</label>
                            <div class="fw-medium">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">User Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>
                        Edit User
                    </a>
                    
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>
                            Delete User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Permissions</h5>
            </div>
            <div class="card-body">
                @if($user->getAllPermissions()->count() > 0)
                    <div class="mb-2">
                        <small class="text-muted">Total Permissions: {{ $user->getAllPermissions()->count() }}</small>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($user->getAllPermissions()->take(10) as $permission)
                            <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                        @endforeach
                        @if($user->getAllPermissions()->count() > 10)
                            <span class="badge bg-secondary">+{{ $user->getAllPermissions()->count() - 10 }} more</span>
                        @endif
                    </div>
                @else
                    <div class="text-muted">No permissions assigned</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
