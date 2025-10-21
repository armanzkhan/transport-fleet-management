@props([
    'fieldType' => 'text',
    'placeholder' => 'Type to search...',
    'name' => '',
    'value' => '',
    'required' => false,
    'class' => 'form-control',
    'id' => null
])

<div class="smart-suggestions-container position-relative">
    <input 
        type="text" 
        class="{{ $class }} smart-suggestions-input" 
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        data-field-type="{{ $fieldType }}"
        {{ $required ? 'required' : '' }}
        {{ $id ? 'id=' . $id : '' }}
        autocomplete="off"
    >
    
    <!-- Suggestions Dropdown -->
    <div class="smart-suggestions-dropdown position-absolute w-100 bg-white border shadow-lg rounded" 
         style="top: 100%; left: 0; z-index: 1050; display: none; max-height: 300px; overflow-y: auto;">
        <div class="smart-suggestions-list"></div>
        <div class="smart-suggestions-loading p-3 text-center" style="display: none;">
            <i class="fas fa-spinner fa-spin me-2"></i>
            Loading suggestions...
        </div>
        <div class="smart-suggestions-no-results p-3 text-muted text-center" style="display: none;">
            <i class="fas fa-search me-2"></i>
            No suggestions found
        </div>
    </div>
</div>

@push('styles')
<style>
.smart-suggestions-dropdown {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.smart-suggestions-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.15s ease-in-out;
}

.smart-suggestions-item:hover,
.smart-suggestions-item.active {
    background-color: #f8f9fa;
}

.smart-suggestions-item:last-child {
    border-bottom: none;
}

.smart-suggestions-item .suggestion-label {
    font-weight: 500;
    color: #212529;
}

.smart-suggestions-item .suggestion-description {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.smart-suggestions-item .suggestion-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    background-color: #e9ecef;
    color: #495057;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeSmartSuggestions();
});

function initializeSmartSuggestions() {
    const containers = document.querySelectorAll('.smart-suggestions-container');
    
    containers.forEach(container => {
        const input = container.querySelector('.smart-suggestions-input');
        const dropdown = container.querySelector('.smart-suggestions-dropdown');
        const list = container.querySelector('.smart-suggestions-list');
        const loading = container.querySelector('.smart-suggestions-loading');
        const noResults = container.querySelector('.smart-suggestions-no-results');
        
        let currentSuggestions = [];
        let selectedIndex = -1;
        let debounceTimer = null;
        
        // Input event handler
        input.addEventListener('input', function() {
            const query = this.value.trim();
            const fieldType = this.dataset.fieldType;
            
            if (query.length < 2) {
                hideDropdown();
                return;
            }
            
            // Debounce the request
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchSuggestions(fieldType, query);
            }, 300);
        });
        
        // Focus event handler
        input.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                showDropdown();
            }
        });
        
        // Blur event handler (with delay to allow clicking on suggestions)
        input.addEventListener('blur', function() {
            setTimeout(() => {
                hideDropdown();
            }, 200);
        });
        
        // Keyboard navigation
        input.addEventListener('keydown', function(e) {
            if (!dropdown.style.display || dropdown.style.display === 'none') {
                return;
            }
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, currentSuggestions.length - 1);
                    updateSelection();
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection();
                    break;
                    
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && selectedIndex < currentSuggestions.length) {
                        selectSuggestion(currentSuggestions[selectedIndex]);
                    }
                    break;
                    
                case 'Escape':
                    hideDropdown();
                    break;
            }
        });
        
        // Fetch suggestions from server
        function fetchSuggestions(fieldType, query) {
            showLoading();
            
            fetch(`{{ route('smart-suggestions.get') }}?field_type=${fieldType}&query=${encodeURIComponent(query)}&limit=10`)
                .then(response => response.json())
                .then(data => {
                    currentSuggestions = data.suggestions || [];
                    displaySuggestions(currentSuggestions);
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    showNoResults();
                });
        }
        
        // Display suggestions
        function displaySuggestions(suggestions) {
            hideLoading();
            
            if (suggestions.length === 0) {
                showNoResults();
                return;
            }
            
            list.innerHTML = '';
            suggestions.forEach((suggestion, index) => {
                const item = createSuggestionItem(suggestion, index);
                list.appendChild(item);
            });
            
            showDropdown();
            selectedIndex = -1;
        }
        
        // Create suggestion item
        function createSuggestionItem(suggestion, index) {
            const item = document.createElement('div');
            item.className = 'smart-suggestions-item';
            item.dataset.index = index;
            
            item.innerHTML = `
                <div class="suggestion-label">${suggestion.label}</div>
                <div class="suggestion-description">${suggestion.description || ''}</div>
                ${suggestion.data ? `<div class="suggestion-badge">${suggestion.type}</div>` : ''}
            `;
            
            item.addEventListener('click', () => {
                selectSuggestion(suggestion);
            });
            
            return item;
        }
        
        // Select suggestion
        function selectSuggestion(suggestion) {
            input.value = suggestion.value;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            hideDropdown();
            
            // Trigger custom event for additional handling
            input.dispatchEvent(new CustomEvent('suggestion-selected', {
                detail: { suggestion: suggestion }
            }));
        }
        
        // Update selection highlighting
        function updateSelection() {
            const items = list.querySelectorAll('.smart-suggestions-item');
            items.forEach((item, index) => {
                item.classList.toggle('active', index === selectedIndex);
            });
        }
        
        // Show/hide dropdown
        function showDropdown() {
            dropdown.style.display = 'block';
        }
        
        function hideDropdown() {
            dropdown.style.display = 'none';
            selectedIndex = -1;
        }
        
        function showLoading() {
            loading.style.display = 'block';
            list.style.display = 'none';
            noResults.style.display = 'none';
            showDropdown();
        }
        
        function hideLoading() {
            loading.style.display = 'none';
            list.style.display = 'block';
        }
        
        function showNoResults() {
            hideLoading();
            noResults.style.display = 'block';
            list.style.display = 'none';
            showDropdown();
        }
    });
}
</script>
@endpush
