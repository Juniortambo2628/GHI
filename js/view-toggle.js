document.addEventListener('DOMContentLoaded', function() {
    const toggleContainer = document.querySelector('.view-toggle');
    if (!toggleContainer) return;

    const storageKey = toggleContainer.dataset.storageKey || 'viewMode';
    const defaultViewMode = toggleContainer.dataset.defaultView || 'grid';
    const contentContainer = document.querySelector('.content-container');
    const viewModeInputs = document.querySelectorAll('input[name="viewMode"]');

    // Load saved preference
    const savedViewMode = localStorage.getItem(storageKey) || defaultViewMode;
    
    // Set initial state
    const activeInput = document.getElementById('view' + savedViewMode.charAt(0).toUpperCase() + savedViewMode.slice(1));
    if (activeInput) {
        activeInput.checked = true;
    }
    
    if (contentContainer) {
        // Remove existing view classes to avoid conflicts
        contentContainer.classList.remove('grid-view', 'list-view');
        contentContainer.classList.add(savedViewMode + '-view');
    }

    // Handle view mode change
    viewModeInputs.forEach(input => {
        input.addEventListener('change', function() {
            const mode = this.value;
            localStorage.setItem(storageKey, mode);
            
            if (contentContainer) {
                contentContainer.classList.remove('grid-view', 'list-view');
                contentContainer.classList.add(mode + '-view');
            }
        });
    });
});
