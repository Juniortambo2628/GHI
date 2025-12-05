/**
 * Form Handler Module
 * Handles form submissions for admin edit/create pages
 * Extracts Quill editor content and FilePond uploads
 */

// Import FilePond for image handling
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import imageCompression from 'browser-image-compression';

// Import FilePond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

// Register FilePond plugins
FilePond.registerPlugin(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageResize
);

/**
 * Initialize FilePond on file inputs
 */
function initializeFilePond() {
  const fileInputs = document.querySelectorAll('.filepond-input');
  
  fileInputs.forEach((input) => {
    if (input.dataset.filePondInitialized) {
      return; // Already initialized
    }

    const pond = FilePond.create(input, {
      labelIdle: 'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
      acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
      maxFileSize: '5MB',
      imageResizeTargetWidth: 1920,
      imageResizeTargetHeight: 1080,
      imageResizeMode: 'contain',
      imageResizeUpscale: false,
      credits: false,
      // Compress images before upload
      beforeAddFile: async (file) => {
        if (file.fileType === 'image') {
          try {
            const compressed = await imageCompression(file.file, {
              maxSizeMB: 1,
              maxWidthOrHeight: 1920,
              useWebWorker: true,
            });
            return compressed;
          } catch (error) {
            console.error('Image compression failed:', error);
            return file;
          }
        }
        return file;
      },
      server: {
        process: {
          url: window.location.origin + '/GHI/admin/api/upload-image.php',
          method: 'POST',
          onload: (response) => {
            const data = JSON.parse(response);
            // Store the uploaded filename in the hidden field
            const hiddenField = input.closest('.mb-3').querySelector('input[type="hidden"]');
            if (hiddenField && data.filename) {
              hiddenField.value = data.filename;
            }
            return data.filename;
          },
          onerror: (response) => {
            console.error('Upload error:', response);
            return response;
          },
        },
        revert: null,
        restore: null,
        load: null,
        fetch: null,
      },
    });

    // Mark as initialized
    input.dataset.filePondInitialized = 'true';
    
    // Store instance for later access
    input._pond = pond;
  });
}

/**
 * Auto-generate slug from title
 */
function initializeSlugGenerator() {
  const titleField = document.getElementById('title');
  const slugField = document.getElementById('slug');
  
  if (titleField && slugField) {
    titleField.addEventListener('blur', function() {
      if (!slugField.value) {
        const slug = this.value
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9-]/g, '-')
          .replace(/-+/g, '-')
          .replace(/^-|-$/g, '');
        slugField.value = slug;
      }
    });
  }
}

/**
 * Handle form submission for all admin forms
 */
function initializeFormSubmission() {
  // Find all forms that need handling
  const forms = [
    'initiativeForm',
    'eventForm',
    'causeForm',
    'storyForm',
    'impactForm'
  ];
  
  forms.forEach((formId) => {
    const form = document.getElementById(formId);
    if (form) {
      form.addEventListener('submit', function(e) {
        // Get Quill editor content
        const editor = this.querySelector('[data-quill-editor]');
        if (editor && window.quillInstances && window.quillInstances[editor]) {
          const quill = window.quillInstances[editor];
          const descriptionField = this.querySelector('#description');
          if (descriptionField) {
            descriptionField.value = quill.root.innerHTML;
          }
        }
        
        // Get FilePond image (if any)
        const filepondInput = this.querySelector('.filepond-input');
        if (filepondInput && filepondInput._pond) {
          const files = filepondInput._pond.getFiles();
          if (files.length > 0) {
            const file = files[0];
            if (file.serverId) {
              // Update the hidden field
              const hiddenField = this.querySelector('input[type="hidden"][name="image"], input[type="hidden"][name="thumbnail"]');
              if (hiddenField) {
                hiddenField.value = file.serverId;
              }
            }
          }
        }
        
        // Form will submit normally
      });
    }
  });
}

/**
 * Initialize all form handlers
 */
function init() {
  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initializeFilePond();
      initializeSlugGenerator();
      initializeFormSubmission();
    });
  } else {
    initializeFilePond();
    initializeSlugGenerator();
    initializeFormSubmission();
  }
}

// Initialize
init();

