/**
 * Enhanced Modal CRUD System
 * Features: AJAX forms, FilePond uploads, auto-save, keyboard shortcuts, dirty checking
 */

import MicroModal from 'micromodal';
import { Notyf } from 'notyf';
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';

// Register FilePond plugins
FilePond.registerPlugin(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageResize
);

// Initialize notification system
const notyf = new Notyf({
  duration: 3000,
  position: { x: 'right', y: 'top' },
});

// Initialize MicroModal
MicroModal.init({
  disableScroll: true,
  disableFocus: false,
  awaitOpenAnimation: true,
  awaitCloseAnimation: true,
  onClose: (modal) => {
    // Check for unsaved changes before closing
    if (modal.id === 'universalModal') {
      const form = modal.querySelector('form[data-ajax-form]');
      if (form && isFormDirty(form)) {
        const confirmClose = confirm(
          'You have unsaved changes. Are you sure you want to close?'
        );
        if (!confirmClose) {
          return false; // Prevent closing
        }
      }
      // Clear auto-save timers
      if (activeAutoSaveTimer) {
        clearTimeout(activeAutoSaveTimer);
        clearInterval(activeAutoSaveInterval);
      }
    }
  },
});

// Auto-save state
let activeAutoSaveTimer = null;
let activeAutoSaveInterval = null;
let initialFormData = new Map();
let autoSaveIndicatorHideTimeout = null;

// FilePond instances
const filePondInstances = new Map();

/**
 * Load form into modal via AJAX
 */
export async function loadFormModal(entity, id = null) {
  const modal = document.getElementById('universalModal');
  const titleEl = document.getElementById('universalModal-title');
  const contentEl = document.getElementById('universalModal-content');

  // Show loading state
  titleEl.textContent = 'Loading...';
  contentEl.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading form...</p>
    </div>
  `;

  // Open modal
  MicroModal.show('universalModal');

  try {
    // Fetch form HTML
    const url = id
      ? `/GHI/admin/api/${entity}-form.php?id=${id}`
      : `/GHI/admin/api/${entity}-form.php`;

    const response = await fetch(url);
    const data = await response.json();

    if (data.success) {
      // Update modal content
      titleEl.textContent = data.title;
      contentEl.innerHTML = data.html;

      // Attach enhancements
      const form = document.getElementById(`modal${capitalize(entity)}Form`);
      if (form) {
        // Store initial form data for dirty checking
        storeInitialFormData(form);

        // Attach form submit handler
        attachFormHandler(form, entity, id);

        // Initialize FilePond for image uploads
        initializeFilePond(form);

        // Initialize Quill editors
        initializeQuillEditors(form);

        // Setup keyboard shortcuts
        setupKeyboardShortcuts(form);

        // Setup auto-save
        setupAutoSave(form, entity, id);

        // Setup dirty checking
        setupDirtyChecking(form);
      }
    } else {
      throw new Error(data.message || 'Failed to load form');
    }
  } catch (error) {
    console.error('Error loading form:', error);
    contentEl.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Failed to load form. Please try again.
      </div>
    `;
    notyf.error('Failed to load form');
  }
}

/**
 * Initialize FilePond for image upload fields
 */
function initializeFilePond(form) {
  const imageInputs = form.querySelectorAll('input[name="image"][type="text"]');

  imageInputs.forEach((input) => {
    // Create FilePond container
    const container = document.createElement('div');
    container.className = 'mb-3';

    const label = document.createElement('label');
    label.className = 'form-label';
    label.textContent = 'Upload Image';

    const filePondInput = document.createElement('input');
    filePondInput.type = 'file';
    filePondInput.className = 'filepond';
    filePondInput.accept = 'image/*';

    container.appendChild(label);
    container.appendChild(filePondInput);

    // Insert before the text input (keep text input as fallback)
    input.parentElement.parentElement.insertBefore(container, input.parentElement);

    // Initialize FilePond
    const pond = FilePond.create(filePondInput, {
      labelIdle:
        'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
      acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
      maxFileSize: '5MB',
      imageResizeTargetWidth: 1920,
      imageResizeTargetHeight: 1080,
      imageResizeMode: 'contain',
      imageResizeUpscale: false,
      server: {
        process: '/GHI/admin/api/upload-image.php',
        revert: null,
        restore: null,
        load: null,
        fetch: null,
      },
      onprocessfile: (error, file) => {
        if (!error) {
          // Update the text input with the uploaded filename
          const response = JSON.parse(file.serverId);
          if (response.filename) {
            input.value = response.filename;
            // Mark form as dirty
            input.dispatchEvent(new Event('input', { bubbles: true }));
          }
        }
      },
    });

    // Store instance for cleanup
    filePondInstances.set(input.id || input.name, pond);

    // Pre-populate if there's an existing image
    if (input.value) {
      // Show preview (optional - would need custom implementation)
      const preview = document.createElement('div');
      preview.className = 'alert alert-info mt-2';
      preview.innerHTML = `
        <i class="bi bi-image me-2"></i>
        Current image: <strong>${input.value}</strong>
      `;
      container.appendChild(preview);
    }
  });
}

/**
 * Initialize Quill editors for rich text fields
 */
function initializeQuillEditors(form) {
  // Check if Quill is available
  if (typeof Quill === 'undefined') {
    console.warn('Quill is not loaded. Rich text editors will not be initialized.');
    return;
  }

  // Find all Quill editor containers
  const quillContainers = form.querySelectorAll('[class*="quill-editor"]');
  
  quillContainers.forEach((container) => {
    // Find the associated hidden textarea
    const textareaId = container.id.replace('_editor', '');
    const textarea = form.querySelector(`#${textareaId}`);
    
    if (!textarea) {
      console.warn(`Textarea not found for Quill container: ${container.id}`);
      return;
    }

    // Determine toolbar based on container class
    const isLarge = container.classList.contains('quill-editor-modal-large');
    const toolbar = isLarge
      ? [
          [{ header: [1, 2, 3, false] }],
          ['bold', 'italic', 'underline'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link', 'blockquote'],
          ['clean'],
        ]
      : [
          ['bold', 'italic', 'underline'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link'],
          ['clean'],
        ];

    // Initialize Quill
    const quill = new Quill(container, {
      theme: 'snow',
      placeholder: isLarge ? 'Tell your story...' : 'Enter description...',
      modules: {
        toolbar: toolbar,
      },
    });

    // Set initial content from textarea
    if (textarea.value) {
      quill.root.innerHTML = textarea.value;
    }

    // Sync Quill content to textarea on form submit
    form.addEventListener('submit', () => {
      textarea.value = quill.root.innerHTML;
    });

    // Also sync on text change for auto-save
    quill.on('text-change', () => {
      textarea.value = quill.root.innerHTML;
      // Trigger input event for dirty checking
      textarea.dispatchEvent(new Event('input', { bubbles: true }));
    });
  });
}

/**
 * Setup keyboard shortcuts
 */
function setupKeyboardShortcuts(form) {
  const keyHandler = (e) => {
    // Ctrl+S or Cmd+S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault();
      form.dispatchEvent(new Event('submit', { cancelable: true }));
      notyf.success('Shortcut: Saving form...');
    }

    // Escape to cancel (already handled by MicroModal)
  };

  form.addEventListener('keydown', keyHandler);

  // Store handler for cleanup
  form.dataset.keyHandler = 'attached';
}

/**
 * Setup auto-save functionality
 */
function setupAutoSave(form, entity, id) {
  if (!id) {
    // Auto-save only works for existing items (editing)
    return;
  }

  let autoSaveDebounce = null;

  const autoSave = async () => {
    const formData = new FormData(form);

    // Add draft flag
    formData.append('auto_save', '1');
    formData.append('status', 'draft');

    try {
      const response = await fetch(`/GHI/admin/api/${entity}-save.php`, {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        // Show subtle auto-save indicator
        showAutoSaveIndicator('saved');
      } else {
        showAutoSaveIndicator('error');
      }
    } catch (error) {
      console.error('Auto-save error:', error);
      showAutoSaveIndicator('error');
    }
  };

  // Debounced auto-save on input
  form.addEventListener('input', () => {
    clearTimeout(autoSaveDebounce);
    showAutoSaveIndicator('saving');

    autoSaveDebounce = setTimeout(() => {
      autoSave();
    }, 2000); // Auto-save 2 seconds after last input
  });

  // Periodic auto-save every 30 seconds
  activeAutoSaveInterval = setInterval(() => {
    if (isFormDirty(form)) {
      autoSave();
    }
  }, 30000);

  // Store timers for cleanup
  activeAutoSaveTimer = autoSaveDebounce;
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator(status) {
  let indicator = document.getElementById('autoSaveIndicator');

  if (!indicator) {
    indicator = document.createElement('div');
    indicator.id = 'autoSaveIndicator';
    indicator.className = 'auto-save-indicator';
    document.body.appendChild(indicator);
  }

  let icon = '';
  let text = '';
  const statusClassMap = {
    saving: 'auto-save-indicator--saving',
    saved: 'auto-save-indicator--saved',
    error: 'auto-save-indicator--error',
  };

  switch (status) {
    case 'saving':
      icon = '<span class="spinner-border spinner-border-sm me-2"></span>';
      text = 'Saving draft...';
      break;
    case 'saved':
      icon = '<i class="bi bi-check-circle me-2"></i>';
      text = 'Draft saved';
      break;
    case 'error':
      icon = '<i class="bi bi-exclamation-triangle me-2"></i>';
      text = 'Auto-save failed';
      break;
    default:
      text = 'Saving...';
      break;
  }

  indicator.innerHTML = icon + text;
  indicator.classList.remove(
    'auto-save-indicator--saving',
    'auto-save-indicator--saved',
    'auto-save-indicator--error'
  );
  if (statusClassMap[status]) {
    indicator.classList.add(statusClassMap[status]);
  }
  indicator.classList.add('is-visible');

  // Fade out after 2 seconds
  if (autoSaveIndicatorHideTimeout) {
    clearTimeout(autoSaveIndicatorHideTimeout);
  }

  autoSaveIndicatorHideTimeout = setTimeout(() => {
    indicator.classList.remove('is-visible');
  }, 2000);
}

/**
 * Setup form dirty checking
 */
function setupDirtyChecking(form) {
  form.addEventListener('input', () => {
    if (isFormDirty(form)) {
      // Add visual indicator
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.classList.contains('btn-warning')) {
        submitBtn.classList.add('btn-warning');
        submitBtn.classList.remove('btn-primary');

        // Add unsaved changes badge
        const badge = document.createElement('span');
        badge.className = 'badge bg-danger ms-2 unsaved-badge';
        badge.textContent = 'Unsaved';
        submitBtn.appendChild(badge);
      }
    }
  });

  // Warn on page unload
  window.addEventListener('beforeunload', (e) => {
    if (isFormDirty(form)) {
      e.preventDefault();
      e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
      return e.returnValue;
    }
  });
}

/**
 * Check if form has unsaved changes
 */
function isFormDirty(form) {
  const formId = form.id;
  const initialData = initialFormData.get(formId);

  if (!initialData) return false;

  const currentData = new FormData(form);
  const currentDataObj = {};

  for (const [key, value] of currentData.entries()) {
    currentDataObj[key] = value;
  }

  // Compare with initial data
  return JSON.stringify(initialData) !== JSON.stringify(currentDataObj);
}

/**
 * Store initial form data for dirty checking
 */
function storeInitialFormData(form) {
  const formData = new FormData(form);
  const dataObj = {};

  for (const [key, value] of formData.entries()) {
    dataObj[key] = value;
  }

  initialFormData.set(form.id, dataObj);
}

/**
 * Attach submit handler to AJAX form
 */
function attachFormHandler(form, entity, id) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');
    const btnText = submitBtn.querySelector('.btn-text');

    // Show loading state
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');

    // Remove unsaved badge
    const unsavedBadge = submitBtn.querySelector('.unsaved-badge');
    if (unsavedBadge) {
      unsavedBadge.remove();
    }

    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach((el) => {
      el.classList.remove('is-invalid');
    });
    form.querySelectorAll('.invalid-feedback').forEach((el) => {
      el.textContent = '';
    });

    try {
      // Prepare form data
      const formData = new FormData(form);
      if (id) {
        formData.append('id', id);
      }

      // Remove auto_save flag if present
      formData.delete('auto_save');

      // Submit via AJAX
      const response = await fetch(`/GHI/admin/api/${entity}-save.php`, {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        // Show success message
        notyf.success(data.message);

        // Clear dirty state
        storeInitialFormData(form);

        // Clear auto-save timers
        if (activeAutoSaveTimer) {
          clearTimeout(activeAutoSaveTimer);
        }
        if (activeAutoSaveInterval) {
          clearInterval(activeAutoSaveInterval);
        }

        // Cleanup FilePond instances
        filePondInstances.forEach((pond) => pond.destroy());
        filePondInstances.clear();

        // Close modal
        MicroModal.close('universalModal');

        // Reload page to show updated data
        setTimeout(() => {
          window.location.reload();
        }, 500);
      } else {
        // Show validation errors
        if (data.errors) {
          Object.keys(data.errors).forEach((field) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
              input.classList.add('is-invalid');
              const feedback = input.parentElement.querySelector('.invalid-feedback');
              if (feedback) {
                feedback.textContent = data.errors[field];
              }
            }
          });
        }

        notyf.error(data.message || 'Validation failed');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      notyf.error('An error occurred. Please try again.');
    } finally {
      // Reset button state
      submitBtn.disabled = false;
      spinner.classList.add('d-none');
      submitBtn.classList.remove('btn-warning');
      submitBtn.classList.add('btn-primary');
    }
  });
}

/**
 * Show delete confirmation modal
 */
export function showDeleteModal(entity, id, name) {
  const modal = document.getElementById('deleteModal');
  const contentEl = document.getElementById('deleteModal-content');
  const confirmBtn = document.getElementById('confirmDelete');

  // Update content
  contentEl.innerHTML = `
    <p>Are you sure you want to delete <strong>${name}</strong>?</p>
    <p class="text-muted mb-0">This action cannot be undone.</p>
  `;

  // Set up confirm button
  const newConfirmBtn = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

  newConfirmBtn.addEventListener('click', async () => {
    newConfirmBtn.disabled = true;
    newConfirmBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

    try {
      const response = await fetch(`/GHI/admin/api/${entity}-delete.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
      });

      const data = await response.json();

      if (data.success) {
        notyf.success(data.message);
        MicroModal.close('deleteModal');

        setTimeout(() => {
          window.location.reload();
        }, 500);
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Delete error:', error);
      notyf.error('Failed to delete item');
      newConfirmBtn.disabled = false;
      newConfirmBtn.innerHTML = '<i class="bi bi-trash me-2"></i>Delete';
    }
  });

  // Show modal
  MicroModal.show('deleteModal');
}

/**
 * Capitalize first letter
 */
function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

// Make functions available globally
if (typeof window !== 'undefined') {
  window.modalCRUD = {
    loadForm: loadFormModal,
    showDelete: showDeleteModal,
  };
}

export default {
  loadForm: loadFormModal,
  showDelete: showDeleteModal,
};
