/**
 * File Upload Module (FilePond)
 * Handles drag-and-drop file uploads with preview
 */

import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';

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
export function initializeFilePond(container = document) {
  const fileInputs = container.querySelectorAll('input[type="file"].filepond');

  fileInputs.forEach((input) => {
    if (input.dataset.filePondInitialized) {
      return; // Already initialized
    }

    const pond = FilePond.create(input, {
      labelIdle:
        'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
      acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
      maxFileSize: '5MB',
      imageResizeTargetWidth: 1920,
      imageResizeTargetHeight: 1080,
      imageResizeMode: 'contain',
      imageResizeUpscale: false,
      credits: false,
      server: {
        process: {
          url: '/GHI/admin/api/upload-image.php',
          method: 'POST',
          onload: (response) => {
            const data = JSON.parse(response);
            // Store the uploaded filename
            if (input.dataset.targetInput) {
              const targetInput = document.getElementById(input.dataset.targetInput);
              if (targetInput) {
                targetInput.value = data.filename;
                targetInput.dispatchEvent(new Event('input', { bubbles: true }));
              }
            }
            return data.filename;
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
    if (!window.filePondInstances) {
      window.filePondInstances = new Map();
    }
    window.filePondInstances.set(input.id || input.name, pond);
  });
}

/**
 * Destroy all FilePond instances
 */
export function destroyFilePond() {
  if (window.filePondInstances) {
    window.filePondInstances.forEach((pond) => {
      pond.destroy();
    });
    window.filePondInstances.clear();
  }
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initializeFilePond();
  });
} else {
  initializeFilePond();
}

/**
 * Compress image before upload
 * @param {File} file - Original image file
 * @param {Object} options - Compression options
 * @returns {Promise<File>} Compressed file
 */
export async function compressImage(file, options = {}) {
  const defaultOptions = {
    maxSizeMB: 1,              // Maximum file size in MB
    maxWidthOrHeight: 1920,     // Maximum width or height
    useWebWorker: true,         // Use web worker for better performance
    fileType: file.type,        // Preserve original file type
  };

  const compressionOptions = { ...defaultOptions, ...options };

  try {
    const compressedFile = await imageCompression(file, compressionOptions);
    if (window.log_message) {
      console.log('Image compressed:', {
        original: (file.size / 1024 / 1024).toFixed(2) + ' MB',
        compressed: (compressedFile.size / 1024 / 1024).toFixed(2) + ' MB',
        reduction: ((1 - compressedFile.size / file.size) * 100).toFixed(1) + '%'
      });
    }
    return compressedFile;
  } catch (error) {
    console.error('Image compression failed:', error);
    // Fallback to original file
    return file;
  }
}

/**
 * Initialize FilePond for images
 */
export function initImage(input, options = {}) {
  if (!input || input.dataset.filePondInitialized) {
    return;
  }

  const defaultOptions = {
    labelIdle: 'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
    maxFileSize: '5MB',
    imageResizeTargetWidth: 1920,
    imageResizeTargetHeight: 1080,
    imageResizeMode: 'contain',
    imageResizeUpscale: false,
    credits: false,
  };

  const pond = FilePond.create(input, {
    ...defaultOptions,
    ...options,
  });

  input.dataset.filePondInitialized = 'true';
  
  if (!window.filePondInstances) {
    window.filePondInstances = new Map();
  }
  window.filePondInstances.set(input.id || input.name, pond);
  
  return pond;
}

/**
 * Initialize FilePond for documents
 */
export function initDocument(input, options = {}) {
  if (!input || input.dataset.filePondInitialized) {
    return;
  }

  const defaultOptions = {
    labelIdle: 'Drag & Drop your document or <span class="filepond--label-action">Browse</span>',
    acceptedFileTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    maxFileSize: '10MB',
    credits: false,
  };

  const pond = FilePond.create(input, {
    ...defaultOptions,
    ...options,
  });

  input.dataset.filePondInitialized = 'true';
  
  if (!window.filePondInstances) {
    window.filePondInstances = new Map();
  }
  window.filePondInstances.set(input.id || input.name, pond);
  
  return pond;
}

/**
 * Initialize FilePond for any file type
 */
export function init(input, options = {}) {
  if (!input || input.dataset.filePondInitialized) {
    return;
  }

  const pond = FilePond.create(input, options);

  input.dataset.filePondInitialized = 'true';
  
  if (!window.filePondInstances) {
    window.filePondInstances = new Map();
  }
  window.filePondInstances.set(input.id || input.name, pond);
  
  return pond;
}

export default {
  initialize: initializeFilePond,
  destroy: destroyFilePond,
  initImage,
  initDocument,
  init,
  compressImage,
};
