@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeTextExpansion();
});

function initializeTextExpansion() {
    let shortcuts = {};
    
    // Load shortcuts from server
    loadShortcuts();
    
    // Add event listeners to all text inputs
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[type="text"], textarea, [contenteditable="true"]')) {
            handleTextExpansion(e.target);
        }
    });
    
    // Load shortcuts from server
    function loadShortcuts() {
        fetch('{{ route("shortcut-dictionary.get-shortcuts") }}')
            .then(response => response.json())
            .then(data => {
                shortcuts = data.shortcuts || {};
            })
            .catch(error => {
                console.error('Error loading shortcuts:', error);
            });
    }
    
    // Handle text expansion
    function handleTextExpansion(element) {
        const value = element.value || element.textContent || '';
        const cursorPosition = element.selectionStart || 0;
        
        // Find the word before cursor that starts with a dot
        const textBeforeCursor = value.substring(0, cursorPosition);
        const words = textBeforeCursor.split(/\s+/);
        const lastWord = words[words.length - 1];
        
        if (lastWord.startsWith('.') && lastWord.length > 1) {
            const shortcut = lastWord.substring(1); // Remove the dot
            
            if (shortcuts[shortcut]) {
                // Replace the shortcut with full form
                const beforeShortcut = textBeforeCursor.substring(0, textBeforeCursor.lastIndexOf(lastWord));
                const afterCursor = value.substring(cursorPosition);
                const newValue = beforeShortcut + shortcuts[shortcut] + afterCursor;
                
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                    element.value = newValue;
                    // Set cursor position after the expanded text
                    const newCursorPosition = beforeShortcut.length + shortcuts[shortcut].length;
                    element.setSelectionRange(newCursorPosition, newCursorPosition);
                } else {
                    element.textContent = newValue;
                    // Set cursor position for contenteditable
                    const range = document.createRange();
                    const sel = window.getSelection();
                    range.setStart(element.firstChild, beforeShortcut.length + shortcuts[shortcut].length);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
                
                // Show expansion notification
                showExpansionNotification(shortcut, shortcuts[shortcut]);
            }
        }
    }
    
    // Show expansion notification
    function showExpansionNotification(shortcut, fullForm) {
        // Remove existing notification
        const existingNotification = document.querySelector('.text-expansion-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'text-expansion-notification position-fixed';
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            font-size: 14px;
            max-width: 300px;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-magic me-2"></i>
                <div>
                    <strong>.${shortcut}</strong> → ${fullForm}
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Global function to reload shortcuts
    window.reloadShortcuts = function() {
        loadShortcuts();
    };
}

// Add visual indicator for shortcuts in text
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS for shortcut highlighting
    const style = document.createElement('style');
    style.textContent = `
        .shortcut-highlight {
            background-color: #fff3cd;
            border-bottom: 1px dotted #ffc107;
            cursor: help;
        }
    `;
    document.head.appendChild(style);
    
    // Function to highlight shortcuts in text
    function highlightShortcuts(element) {
        if (!element || element.tagName === 'SCRIPT' || element.tagName === 'STYLE') {
            return;
        }
        
        const text = element.textContent;
        if (!text) return;
        
        // Find all dot-prefixed words
        const shortcutRegex = /\.\w+/g;
        const matches = text.match(shortcutRegex);
        
        if (matches) {
            matches.forEach(match => {
                const shortcut = match.substring(1);
                // Check if this is a known shortcut
                fetch('{{ route("shortcut-dictionary.get-shortcuts") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.shortcuts && data.shortcuts[shortcut]) {
                            // Add tooltip or highlight
                            const regex = new RegExp(match.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
                            element.innerHTML = element.innerHTML.replace(regex, 
                                `<span class="shortcut-highlight" title="Expands to: ${data.shortcuts[shortcut]}">${match}</span>`
                            );
                        }
                    });
            });
        }
    }
    
    // Apply highlighting to all text elements
    const textElements = document.querySelectorAll('p, div, span, td, th, li');
    textElements.forEach(highlightShortcuts);
});
</script>
@endpush
