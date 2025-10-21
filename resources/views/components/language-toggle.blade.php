<div class="language-toggle">
    <div class="btn-group" role="group">
        <button type="button" 
                class="btn btn-outline-secondary btn-sm language-btn {{ app('App\Services\LanguageService')::getCurrentLanguage() === 'en' ? 'active' : '' }}"
                data-language="en"
                title="English">
            <i class="fas fa-globe me-1"></i>
            EN
        </button>
        <button type="button" 
                class="btn btn-outline-secondary btn-sm language-btn {{ app('App\Services\LanguageService')::getCurrentLanguage() === 'ur' ? 'active' : '' }}"
                data-language="ur"
                title="اردو">
            <i class="fas fa-globe me-1"></i>
            اردو
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const languageButtons = document.querySelectorAll('.language-btn');
    
    languageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const language = this.dataset.language;
            
            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
            this.disabled = true;
            
            // Switch language
            fetch('{{ route("language.switch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ language: language })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update active state
                    languageButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Trigger dynamic translation
                    document.dispatchEvent(new CustomEvent('languageChanged', {
                        detail: { language: language }
                    }));
                    
                    // Reload page to apply language changes
                    window.location.reload();
                } else {
                    alert('Failed to switch language: ' + data.message);
                    // Restore button state
                    this.innerHTML = '<i class="fas fa-globe me-1"></i>' + (language === 'en' ? 'EN' : 'اردو');
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Language switch error:', error);
                alert('Failed to switch language');
                // Restore button state
                this.innerHTML = '<i class="fas fa-globe me-1"></i>' + (language === 'en' ? 'EN' : 'اردو');
                this.disabled = false;
            });
        });
    });
});
</script>
@endpush
