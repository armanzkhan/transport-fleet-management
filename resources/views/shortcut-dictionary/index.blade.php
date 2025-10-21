@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Shortcut Dictionary</h2>
                <p class="text-muted mb-0">Manage text expansion shortcuts and abbreviations.</p>
            </div>
            <div>
                @can('manage-shortcuts')
                <a href="{{ route('shortcut-dictionary.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Shortcut
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('shortcut-dictionary.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Shortcut, full form, description...">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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

<!-- Shortcuts Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Shortcut</th>
                                <th>Full Form</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shortcuts as $shortcut)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">.{{ $shortcut->shortcut }}</code>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $shortcut->full_form }}</span>
                                </td>
                                <td>{{ $shortcut->description ?? 'No description' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($shortcut->category) }}</span>
                                </td>
                                <td>
                                    @if($shortcut->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $shortcut->creator->name ?? 'System' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('manage-shortcuts')
                                        <a href="{{ route('shortcut-dictionary.edit', $shortcut) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('shortcut-dictionary.destroy', $shortcut) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this shortcut?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
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
                                        <i class="fas fa-keyboard fa-3x mb-3"></i>
                                        <h5>No Shortcuts Found</h5>
                                        <p>Start by creating your first shortcut.</p>
                                        @can('manage-shortcuts')
                                        <a href="{{ route('shortcut-dictionary.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Add Shortcut
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
            
            @if($shortcuts->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $shortcuts->firstItem() ?? 0 }} to {{ $shortcuts->lastItem() ?? 0 }} of {{ $shortcuts->total() }} shortcuts
                    </div>
                    <div>
                        {{ $shortcuts->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Usage Instructions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-info bg-opacity-10">
            <div class="card-body">
                <h6 class="text-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    How to Use Text Expansion
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Usage:</h6>
                        <ul class="list-unstyled">
                            <li><code>.company</code> → Expands to full company name</li>
                            <li><code>.address</code> → Expands to full address</li>
                            <li><code>.signature</code> → Expands to your signature</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Tips:</h6>
                        <ul class="list-unstyled">
                            <li>• Type a dot (.) followed by your shortcut</li>
                            <li>• Shortcuts are case-sensitive</li>
                            <li>• Use descriptive shortcuts for better recall</li>
                            <li>• Organize shortcuts by categories</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
