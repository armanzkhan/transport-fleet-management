/**
 * Keyboard Shortcuts for Transport Fleet Management System
 * Implements system-wide keyboard shortcuts as per SRS 1.27
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
});

function initializeKeyboardShortcuts() {
    // Ctrl+S - Save
    document.addEventListener('keydown', function(e) {
        // Only trigger if not typing in input/textarea/select
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            // Allow Ctrl+S to work in text inputs
            if (e.ctrlKey && e.key === 's' && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) {
                return true; // Let browser handle it
            }
            return;
        }

        // Ctrl+S - Save (Submit form or trigger save button)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('button[type="submit"], button.save, .btn-save, button.btn-primary');
            if (saveButton) {
                saveButton.click();
            } else {
                // Try to find form and submit it
                const form = document.querySelector('form');
                if (form) {
                    form.requestSubmit();
                }
            }
        }

        // Ctrl+P - Print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            const printButton = document.querySelector('button.print, .btn-print, a.print, .print-btn');
            if (printButton) {
                printButton.click();
            } else {
                // Default browser print
                window.print();
            }
        }

        // Ctrl+E - Export
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            const exportButton = document.querySelector('button.export, .btn-export, a.export, .export-btn, .dropdown-item[href*="export"]');
            if (exportButton) {
                exportButton.click();
            }
        }

        // Ctrl+N - New/Create
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const newButton = document.querySelector('a[href*="create"], button.create, .btn-create, .add-btn');
            if (newButton) {
                newButton.click();
            }
        }

        // Ctrl+F - Focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="search"], input.search, input[placeholder*="Search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        // Ctrl+/ or Ctrl+? - Show keyboard shortcuts help
        if ((e.ctrlKey && e.key === '/') || (e.ctrlKey && e.key === '?')) {
            e.preventDefault();
            showKeyboardShortcutsHelp();
        }

        // Escape - Close modals, cancel forms
        if (e.key === 'Escape') {
            const modal = document.querySelector('.modal.show');
            if (modal) {
                const closeButton = modal.querySelector('[data-bs-dismiss="modal"], .btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }

        // Tab navigation enhancement
        if (e.key === 'Tab' && !e.shiftKey) {
            // Allow normal tab navigation
            // Focus management is handled by browser
        }

        // Enter in dropdowns
        if (e.key === 'Enter') {
            const focused = document.activeElement;
            if (focused && focused.classList.contains('dropdown-toggle')) {
                e.preventDefault();
                focused.click();
            }
        }
    });

    // Add visual feedback for keyboard shortcuts
    addKeyboardShortcutHints();
}

function showKeyboardShortcutsHelp() {
    const shortcuts = [
        { key: 'Ctrl+S', action: 'Save / Submit Form' },
        { key: 'Ctrl+P', action: 'Print' },
        { key: 'Ctrl+E', action: 'Export' },
        { key: 'Ctrl+N', action: 'New / Create' },
        { key: 'Ctrl+F', action: 'Focus Search' },
        { key: 'Ctrl+/ or Ctrl+?', action: 'Show Keyboard Shortcuts Help' },
        { key: 'Escape', action: 'Close Modal / Cancel' },
        { key: 'Tab', action: 'Navigate Fields' },
        { key: 'Enter', action: 'Submit / Confirm Selection' },
        { key: 'Arrow Keys', action: 'Navigate Dropdowns' },
    ];

    // Create modal or alert with shortcuts
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keyboard Shortcuts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Shortcut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${shortcuts.map(s => `
                                <tr>
                                    <td><kbd>${s.key}</kbd></td>
                                    <td>${s.action}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Initialize Bootstrap modal if available
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        modal.addEventListener('hidden.bs.modal', function() {
            modal.remove();
        });
    } else {
        // Fallback: use simple show/hide
        modal.style.display = 'block';
        modal.querySelector('.btn-close, .btn-secondary').addEventListener('click', function() {
            modal.remove();
        });
    }
}

function addKeyboardShortcutHints() {
    // Add keyboard shortcut hints to buttons
    const buttons = document.querySelectorAll('button[type="submit"], .btn-save, button.save');
    buttons.forEach(btn => {
        if (!btn.querySelector('.shortcut-hint')) {
            const hint = document.createElement('small');
            hint.className = 'shortcut-hint text-muted ms-2';
            hint.textContent = '(Ctrl+S)';
            hint.style.fontSize = '0.75rem';
            btn.appendChild(hint);
        }
    });

    const printButtons = document.querySelectorAll('button.print, .btn-print, a.print');
    printButtons.forEach(btn => {
        if (!btn.querySelector('.shortcut-hint')) {
            const hint = document.createElement('small');
            hint.className = 'shortcut-hint text-muted ms-2';
            hint.textContent = '(Ctrl+P)';
            hint.style.fontSize = '0.75rem';
            btn.appendChild(hint);
        }
    });
}

// Export for use in other scripts
window.keyboardShortcuts = {
    initialize: initializeKeyboardShortcuts,
    showHelp: showKeyboardShortcutsHelp
};

