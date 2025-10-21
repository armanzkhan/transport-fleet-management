@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Edit Shortcut</h2>
                <p class="text-muted mb-0">Update shortcut: <code>.{{ $shortcutDictionary->shortcut }}</code></p>
            </div>
            <div>
                <a href="{{ route('shortcut-dictionary.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('shortcut-dictionary.update', $shortcutDictionary) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-keyboard me-2"></i>
                        Shortcut Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="shortcut" class="form-label">Shortcut <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">.</span>
                                <input type="text" class="form-control" id="shortcut" name="shortcut" 
                                       value="{{ old('shortcut', $shortcutDictionary->shortcut) }}" placeholder="company" required>
                            </div>
                            <div class="form-text">Enter the shortcut without the dot (.)</div>
                            @error('shortcut')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="general" {{ old('category', $shortcutDictionary->category) == 'general' ? 'selected' : '' }}>General</option>
                                <option value="company" {{ old('category', $shortcutDictionary->category) == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="address" {{ old('category', $shortcutDictionary->category) == 'address' ? 'selected' : '' }}>Address</option>
                                <option value="signature" {{ old('category', $shortcutDictionary->category) == 'signature' ? 'selected' : '' }}>Signature</option>
                                <option value="technical" {{ old('category', $shortcutDictionary->category) == 'technical' ? 'selected' : '' }}>Technical</option>
                                <option value="custom" {{ old('category', $shortcutDictionary->category) == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('category')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_form" class="form-label">Full Form <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="full_form" name="full_form" rows="3" 
                                  placeholder="Enter the full text that will replace the shortcut..." required>{{ old('full_form', $shortcutDictionary->full_form) }}</textarea>
                        @error('full_form')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" 
                                  placeholder="Optional description of what this shortcut is used for...">{{ old('description', $shortcutDictionary->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $shortcutDictionary->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (Enable this shortcut)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Shortcut:</label>
                        <code class="bg-light px-2 py-1 rounded" id="shortcut-preview">.{{ $shortcutDictionary->shortcut }}</code>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expansion:</label>
                        <div class="border rounded p-2 bg-light" id="expansion-preview">
                            {{ $shortcutDictionary->full_form }}
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Usage:</strong> Type <code>.{{ $shortcutDictionary->shortcut }}</code> in any text field and it will automatically expand to the full form.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Shortcut History
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <div><strong>Created:</strong> {{ $shortcutDictionary->created_at->format('M d, Y H:i') }}</div>
                        <div><strong>Created by:</strong> {{ $shortcutDictionary->creator->name ?? 'System' }}</div>
                        <div><strong>Last updated:</strong> {{ $shortcutDictionary->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('shortcut-dictionary.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </a>
                <div>
                    <button type="button" class="btn btn-outline-info me-2" onclick="testShortcut()">
                        <i class="fas fa-play me-2"></i>
                        Test Shortcut
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Update Shortcut
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Update preview as user types
document.getElementById('shortcut').addEventListener('input', function() {
    const shortcut = this.value;
    document.getElementById('shortcut-preview').textContent = '.' + shortcut;
});

document.getElementById('full_form').addEventListener('input', function() {
    const fullForm = this.value;
    document.getElementById('expansion-preview').textContent = fullForm;
});

// Test shortcut functionality
function testShortcut() {
    const shortcut = document.getElementById('shortcut').value;
    const fullForm = document.getElementById('full_form').value;
    
    if (!shortcut || !fullForm) {
        alert('Please enter both shortcut and full form to test.');
        return;
    }
    
    alert(`Shortcut test: .${shortcut} → ${fullForm}`);
}
</script>
@endpush
@endsection
