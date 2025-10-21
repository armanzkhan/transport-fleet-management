@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Create Shortcut</h2>
                <p class="text-muted mb-0">Add a new text expansion shortcut to the dictionary.</p>
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

<form method="POST" action="{{ route('shortcut-dictionary.store') }}">
    @csrf
    
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
                                       value="{{ old('shortcut') }}" placeholder="company" required>
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
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="company" {{ old('category') == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="address" {{ old('category') == 'address' ? 'selected' : '' }}>Address</option>
                                <option value="signature" {{ old('category') == 'signature' ? 'selected' : '' }}>Signature</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                                <option value="custom" {{ old('category') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('category')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_form" class="form-label">Full Form <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="full_form" name="full_form" rows="3" 
                                  placeholder="Enter the full text that will replace the shortcut..." required>{{ old('full_form') }}</textarea>
                        @error('full_form')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" 
                                  placeholder="Optional description of what this shortcut is used for...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
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
                        <code class="bg-light px-2 py-1 rounded" id="shortcut-preview">.shortcut</code>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expansion:</label>
                        <div class="border rounded p-2 bg-light" id="expansion-preview">
                            Type your shortcut to see preview...
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Usage:</strong> Type <code>.shortcut</code> in any text field and it will automatically expand to the full form.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li>• Use descriptive shortcuts</li>
                        <li>• Keep shortcuts short but memorable</li>
                        <li>• Test your shortcuts before saving</li>
                        <li>• Organize by categories</li>
                        <li>• Use consistent naming patterns</li>
                    </ul>
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
                        Save Shortcut
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
    document.getElementById('expansion-preview').textContent = fullForm || 'Type your shortcut to see preview...';
});

// Test shortcut functionality
function testShortcut() {
    const shortcut = document.getElementById('shortcut').value;
    const fullForm = document.getElementById('full_form').value;
    
    if (!shortcut || !fullForm) {
        alert('Please enter both shortcut and full form to test.');
        return;
    }
    
    // Create a test input
    const testInput = document.createElement('input');
    testInput.type = 'text';
    testInput.value = '.' + shortcut;
    testInput.style.position = 'absolute';
    testInput.style.left = '-9999px';
    document.body.appendChild(testInput);
    
    // Simulate the expansion
    setTimeout(() => {
        testInput.value = fullForm;
        alert(`Shortcut test: .${shortcut} → ${fullForm}`);
        document.body.removeChild(testInput);
    }, 100);
}
</script>
@endpush
@endsection
