@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeKeyboardNavigation();
});

function initializeKeyboardNavigation() {
    // Global keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + S for Save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('button[type="submit"], .btn-primary, .btn-success');
            if (saveButton && !saveButton.disabled) {
                saveButton.click();
            }
        }
        
        // Ctrl + P for Print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        
        // Ctrl + N for New/Create
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const createButton = document.querySelector('a[href*="create"], .btn-primary[href*="create"]');
            if (createButton) {
                createButton.click();
            }
        }
        
        // Ctrl + F for Search/Filter
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="search"], input[name="search"], .search-input');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Escape to close modals/dropdowns
        if (e.key === 'Escape') {
            // Close any open modals
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
            
            // Close any open dropdowns
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
    
    // Tab navigation enhancement
    enhanceTabNavigation();
    
    // Form navigation
    enhanceFormNavigation();
    
    // Table navigation
    enhanceTableNavigation();
    
    // Button navigation
    enhanceButtonNavigation();
}

function enhanceTabNavigation() {
    // Add tabindex to all interactive elements
    const interactiveElements = document.querySelectorAll(
        'input, select, textarea, button, a, [tabindex], [onclick], [data-bs-toggle]'
    );
    
    interactiveElements.forEach((element, index) => {
        if (!element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '0');
        }
    });
    
    // Enhanced tab navigation with visual focus
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            // Add visual focus indicator
            setTimeout(() => {
                const focusedElement = document.activeElement;
                if (focusedElement) {
                    focusedElement.style.outline = '2px solid #0d6efd';
                    focusedElement.style.outlineOffset = '2px';
                }
            }, 10);
        }
    });
    
    // Remove focus outline on mouse interaction
    document.addEventListener('mousedown', function() {
        const focusedElement = document.activeElement;
        if (focusedElement) {
            focusedElement.style.outline = '';
            focusedElement.style.outlineOffset = '';
        }
    });
}

function enhanceFormNavigation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach((input, index) => {
            // Add Enter key support for form submission
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // If it's a submit button, click it
                    if (this.type === 'submit') {
                        this.click();
                        return;
                    }
                    
                    // If it's the last input, submit the form
                    if (index === inputs.length - 1) {
                        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                        if (submitButton) {
                            submitButton.click();
                        }
                        return;
                    }
                    
                    // Otherwise, move to next input
                    const nextInput = inputs[index + 1];
                    if (nextInput) {
                        nextInput.focus();
                    }
                }
            });
        });
    });
}

function enhanceTableNavigation() {
    const tables = document.querySelectorAll('table');
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        const cells = table.querySelectorAll('td, th');
        
        // Arrow key navigation for table cells
        cells.forEach(cell => {
            cell.addEventListener('keydown', function(e) {
                const currentRow = this.closest('tr');
                const currentCell = this;
                const cellIndex = Array.from(currentRow.children).indexOf(currentCell);
                
                let targetCell = null;
                
                switch(e.key) {
                    case 'ArrowUp':
                        e.preventDefault();
                        const prevRow = currentRow.previousElementSibling;
                        if (prevRow) {
                            targetCell = prevRow.children[cellIndex];
                        }
                        break;
                        
                    case 'ArrowDown':
                        e.preventDefault();
                        const nextRow = currentRow.nextElementSibling;
                        if (nextRow) {
                            targetCell = nextRow.children[cellIndex];
                        }
                        break;
                        
                    case 'ArrowLeft':
                        e.preventDefault();
                        targetCell = currentCell.previousElementSibling;
                        break;
                        
                    case 'ArrowRight':
                        e.preventDefault();
                        targetCell = currentCell.nextElementSibling;
                        break;
                        
                    case 'Enter':
                        e.preventDefault();
                        // Activate the first link or button in the cell
                        const link = currentCell.querySelector('a, button');
                        if (link) {
                            link.click();
                        }
                        break;
                }
                
                if (targetCell) {
                    targetCell.focus();
                }
            });
        });
    });
}

function enhanceButtonNavigation() {
    const buttons = document.querySelectorAll('button, .btn, a[role="button"]');
    
    buttons.forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

// Keyboard shortcuts help modal
function showKeyboardShortcuts() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-keyboard me-2"></i>
                        Keyboard Shortcuts
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>General Shortcuts</h6>
                            <ul class="list-unstyled">
                                <li><kbd>Ctrl</kbd> + <kbd>S</kbd> - Save</li>
                                <li><kbd>Ctrl</kbd> + <kbd>P</kbd> - Print</li>
                                <li><kbd>Ctrl</kbd> + <kbd>N</kbd> - New/Create</li>
                                <li><kbd>Ctrl</kbd> + <kbd>F</kbd> - Search/Filter</li>
                                <li><kbd>Esc</kbd> - Close modals/dropdowns</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Navigation</h6>
                            <ul class="list-unstyled">
                                <li><kbd>Tab</kbd> - Next element</li>
                                <li><kbd>Shift</kbd> + <kbd>Tab</kbd> - Previous element</li>
                                <li><kbd>Enter</kbd> - Activate button/link</li>
                                <li><kbd>Space</kbd> - Activate button</li>
                                <li><kbd>↑↓←→</kbd> - Navigate tables</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Add keyboard shortcuts help button to navigation
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar-nav');
    if (navbar) {
        const helpItem = document.createElement('li');
        helpItem.className = 'nav-item';
        helpItem.innerHTML = `
            <a class="nav-link" href="#" onclick="showKeyboardShortcuts(); return false;" title="Keyboard Shortcuts">
                <i class="fas fa-keyboard"></i>
            </a>
        `;
        navbar.appendChild(helpItem);
    }
});
</script>
@endpush
