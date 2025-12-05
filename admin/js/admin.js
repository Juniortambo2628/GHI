/**
 * Admin Dashboard JavaScript
 * Global Harmony Initiative Admin Dashboard
 * Uses modern packages: Chart.js, Tabulator, Quill, FilePond, Notyf, etc.
 */

// Import modern packages
import chartService from '../../js/charts.js';
import tableService from '../../js/tables.js';
import editorService from '../../js/editor.js';
import fileUploadService from '../../js/file-upload.js';
import notifications from '../../js/notifications.js';
import apiService from '../../js/api.js';
import { validate, formSchemas } from '../../js/validation.js';
import utils from '../../js/utils.js';
import modalService from '../../js/modals.js';
import modalCRUD from '../../js/modal-crud.js';
import errorTracking from '../../js/error-tracking.js';
import { exportTabulatorToExcel, exportToExcel } from '../../js/excel-export.js';
import { generatePDFFromTabulator, generatePDF } from '../../js/pdf-generator.js';
import { initGridSortable, addDragHandles } from '../../js/drag-sort.js';

// Initialize error tracking
const sentryDsn = document.querySelector('meta[name="sentry-dsn"]')?.getAttribute('content');
if (sentryDsn) {
  errorTracking.init(sentryDsn, {
    environment: 'admin',
  });
}

document.addEventListener('DOMContentLoaded', function () {
  // Initialize all admin features
  initializeCharts();
  initializeTables();
  initializeEditors();
  initializeFileUploads();
  initializeForms();
  initializeSearch();
  initializeAutoSubmitFilters();
  initializeViewToggles();
  initializeGridSortable();
  initializeModals();
  initializeModalButtons();
  initializeExportButtons();
});

/**
 * Initialize Chart.js charts with real data from data attributes
 */
function initializeCharts() {
  // Find all canvas elements with data-chart attribute
  document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
    const chartType = canvas.getAttribute('data-chart');
    const dataJson = canvas.getAttribute('data-chart-data');
    const optionsJson = canvas.getAttribute('data-chart-options');

    if (!dataJson) {
      console.warn('Chart element missing data-chart-data attribute:', canvas.id);
      return;
    }

    try {
      const data = JSON.parse(dataJson);
      const options = optionsJson ? JSON.parse(optionsJson) : {};

      // Create chart based on type
      switch (chartType) {
        case 'pie':
          chartService.pie(canvas, data, options);
          break;
        case 'doughnut':
          chartService.doughnut(canvas, data, options);
          break;
        case 'bar':
          chartService.bar(canvas, data, options);
          break;
        case 'line':
          chartService.line(canvas, data, options);
          break;
        case 'area':
          // Area chart is just a line chart with fill
          chartService.line(canvas, data, {
            ...options,
            fill: true,
          });
          break;
        default:
          console.warn('Unknown chart type:', chartType);
      }
    } catch (e) {
      console.error('Error initializing chart:', canvas.id, e);
    }
  });
}

/**
 * Attach delete confirmation handlers to table container
 */
function attachDeleteHandlers(container, table) {
  // Use event delegation on the container
  container.addEventListener('click', function(e) {
    const deleteLink = e.target.closest('a[data-delete-confirm]');
    if (deleteLink) {
      e.preventDefault();
      e.stopPropagation();
      const message = deleteLink.getAttribute('data-delete-confirm') || 'Are you sure you want to delete this item?';
      if (confirm(message)) {
        window.location.href = deleteLink.getAttribute('href');
      }
    }
  });
}

/**
 * Initialize Tabulator tables
 */
function initializeTables() {
  document.querySelectorAll('[data-tabulator]').forEach((container) => {
    const ajaxUrl = container.getAttribute('data-ajax-url');
    const columnsJson = container.getAttribute('data-columns');
    const dataJson = container.getAttribute('data-table-data');
    const shouldIncludeActions = container.getAttribute('data-actions') !== 'false';

    container.dataset.tableBuilt = container.dataset.tableBuilt || 'false';

    const notifyTableReady = (tableInstance) => {
      if (tableInstance && typeof tableInstance.on === 'function') {
        tableInstance.on('tableBuilt', () => {
          container.dataset.tableBuilt = 'true';
          container.dispatchEvent(new CustomEvent('tabulator:built', { detail: { table: tableInstance } }));
        });
      }
    };

    if (!columnsJson) {
      console.warn('Table missing columns definition:', container.id || 'unnamed');
      return;
    }

    try {
      let columns = JSON.parse(columnsJson);
      const tableData = dataJson ? JSON.parse(dataJson) : null;

      const hasActionColumn = columns.some((col) => col.field === 'action_menu' || col.formatter === 'actionMenu');
      if (shouldIncludeActions && !hasActionColumn) {
        columns.push({
          title: '',
          field: 'action_menu',
          formatter: 'actionMenu',
          width: 60,
          hozAlign: 'center',
          headerSort: false,
          cssClass: 'table-action-column',
        });
      }

      if (ajaxUrl) {
        const table = tableService.initFromAjax(container, ajaxUrl, columns, {
          onError: (error) => {
            notifications.error('Failed to load table data');
            console.error('Table error:', error);
          },
        });
        container.tabulator = table;
        attachDeleteHandlers(container, table);
        notifyTableReady(table);
      } else if (tableData) {
        const table = tableService.initFromData(container, tableData, columns);
        container.tabulator = table;
        attachDeleteHandlers(container, table);
        notifyTableReady(table);
      } else {
        console.warn('No data source provided for table');
      }
    } catch (error) {
      console.error('Error initializing table:', error);
      notifications.error('Failed to initialize table: ' + error.message);
    }
  });
}

/**
 * Initialize export buttons for tables
 */
function initializeExportButtons() {
  // Excel export buttons
  document.querySelectorAll('[data-export-excel]').forEach((button) => {
    button.addEventListener('click', () => {
      const tableId = button.getAttribute('data-table-id') || button.getAttribute('data-export-excel');
      const tableContainer = document.getElementById(tableId);
      
      if (!tableContainer || !tableContainer.tabulator) {
        notifications.error('Table not found or not initialized');
        return;
      }

      const filename = button.getAttribute('data-filename') || `${tableId}-export.xlsx`;
      
      try {
        exportTabulatorToExcel(tableContainer.tabulator, filename);
        notifications.success('Excel export started');
      } catch (error) {
        console.error('Excel export failed:', error);
        notifications.error('Failed to export Excel file');
      }
    });
  });

  // PDF export buttons
  document.querySelectorAll('[data-export-pdf]').forEach((button) => {
    button.addEventListener('click', () => {
      const tableId = button.getAttribute('data-table-id') || button.getAttribute('data-export-pdf');
      const tableContainer = document.getElementById(tableId);
      
      if (!tableContainer || !tableContainer.tabulator) {
        notifications.error('Table not found or not initialized');
        return;
      }

      const filename = button.getAttribute('data-filename') || `${tableId}-report.pdf`;
      
      try {
        generatePDFFromTabulator(tableContainer.tabulator, filename);
        notifications.success('PDF export started');
      } catch (error) {
        console.error('PDF export failed:', error);
        notifications.error('Failed to export PDF file');
      }
    });
  });

  // CSV export buttons (server-side)
  document.querySelectorAll('[data-export-csv]').forEach((button) => {
    button.addEventListener('click', async () => {
      const tableId = button.getAttribute('data-table-id') || button.getAttribute('data-export-csv');
      const tableContainer = document.getElementById(tableId);
      
      if (!tableContainer || !tableContainer.tabulator) {
        notifications.error('Table not found or not initialized');
        return;
      }

      const filename = button.getAttribute('data-filename') || `${tableId}-export.csv`;
      const tableData = tableContainer.tabulator.getData();
      
      try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Send data to server for CSV export
        const response = await fetch(`${window.location.origin}/GHI/admin/api/export-csv.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
          },
          body: JSON.stringify({
            data: tableData,
            filename: filename,
          }),
        });

        if (!response.ok) {
          throw new Error('Export failed');
        }

        // Download the CSV file
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        notifications.success('CSV export completed');
      } catch (error) {
        console.error('CSV export failed:', error);
        notifications.error('Failed to export CSV file');
      }
    });
  });
}

/**
 * Initialize view toggles (grid/table) without page reload
 */
function initializeViewToggles() {
  document.querySelectorAll('[data-view-key]').forEach((switcher) => {
    const storageKey = switcher.getAttribute('data-view-key');
    const defaultView = switcher.getAttribute('data-default-view') || 'table';
    const container = document.querySelector(`[data-view-container="${storageKey}"]`);
    if (!storageKey || !container) {
      return;
    }

    const buttons = switcher.querySelectorAll('[data-view-mode]');
    const sections = container.querySelectorAll('[data-view-mode]');
    const persistedKey = `view:${storageKey}`;

    const requestTableRedraw = (tableContainer) => {
      if (!tableContainer) {
        return;
      }

      const redraw = () => {
        if (tableContainer.tabulator) {
          tableContainer.tabulator.redraw(true);
        }
      };

      if (tableContainer.dataset.tableBuilt === 'true') {
        requestAnimationFrame(redraw);
      } else {
        const handleBuilt = () => {
          tableContainer.removeEventListener('tabulator:built', handleBuilt);
          requestAnimationFrame(redraw);
        };
        tableContainer.addEventListener('tabulator:built', handleBuilt);
      }
    };

    const setView = (mode) => {
      localStorage.setItem(persistedKey, mode);
      buttons.forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-view-mode') === mode);
      });
      sections.forEach((section) => {
        const isActive = section.getAttribute('data-view-mode') === mode;
        section.classList.toggle('d-none', !isActive);
        if (isActive) {
          if (mode === 'table') {
            requestTableRedraw(section.querySelector('[data-tabulator]'));
          } else if (mode === 'grid') {
            // Reinitialize grid sortable when grid view is shown
            const gridContainer = section.querySelector('[data-sortable-grid]');
            if (gridContainer) {
              const entityType = gridContainer.getAttribute('data-entity-type');
              if (entityType) {
                // Remove existing sortable instance if any
                if (gridContainer.sortableInstance) {
                  gridContainer.sortableInstance.destroy();
                }
                // Add drag handles
                addDragHandles(gridContainer);
                // Initialize sortable
                gridContainer.sortableInstance = initGridSortable(gridContainer, {
                  entityType: entityType,
                  sortableOptions: {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                  }
                });
              }
            }
          }
        }
      });
    };

    const savedView = localStorage.getItem(persistedKey) || defaultView;
    setView(savedView);

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const mode = button.getAttribute('data-view-mode') || 'table';
        setView(mode);
      });
    });
  });
}

/**
 * Initialize grid sortable (drag-and-drop for grid views)
 */
function initializeGridSortable() {
  // Find all grid containers with sortable attribute
  document.querySelectorAll('[data-sortable-grid]').forEach((container) => {
    const entityType = container.getAttribute('data-entity-type');
    if (!entityType) {
      return;
    }

    // Add drag handles to items
    addDragHandles(container);

    // Initialize SortableJS
    initGridSortable(container, {
      entityType: entityType,
      sortableOptions: {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
      }
    });
  });

  // Also initialize for grid views that are shown after view toggle
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1 && node.hasAttribute && node.hasAttribute('data-sortable-grid')) {
          const entityType = node.getAttribute('data-entity-type');
          if (entityType) {
            addDragHandles(node);
            initGridSortable(node, {
              entityType: entityType,
              sortableOptions: {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
              }
            });
          }
        }
      });
    });
  });

  // Observe view containers for dynamically shown grids
  document.querySelectorAll('[data-view-container]').forEach((container) => {
    observer.observe(container, { childList: true, subtree: true });
  });
}

/**
 * Initialize Quill editors
 */
function initializeEditors() {
  document.querySelectorAll('[data-quill-editor]').forEach((container) => {
    const options = {
      placeholder: container.getAttribute('data-placeholder') || 'Start typing...',
      readOnly: container.hasAttribute('data-readonly'),
    };

    editorService.init(container, options);
  });
}

/**
 * Initialize FilePond file uploads
 */
function initializeFileUploads() {
  // Check if fileUploadService has required methods
  if (!fileUploadService || typeof fileUploadService.initImage !== 'function') {
    console.warn('FilePond service not available or not properly loaded');
    return;
  }

  try {
    // Image uploads
    document.querySelectorAll('[data-filepond="image"]').forEach((input) => {
      try {
        fileUploadService.initImage(input, {
          server: {
            url: `${window.location.origin}/GHI/admin/api/upload-image.php`,
            process: {
              headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              },
            },
          },
        });
      } catch (e) {
        console.error('Error initializing image upload:', e);
      }
    });

    // Document uploads
    document.querySelectorAll('[data-filepond="document"]').forEach((input) => {
      try {
        if (typeof fileUploadService.initDocument === 'function') {
          fileUploadService.initDocument(input, {
            server: {
              url: `${window.location.origin}/GHI/admin/api/upload-document.php`,
              process: {
                headers: {
                  'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
              },
            },
          });
        }
      } catch (e) {
        console.error('Error initializing document upload:', e);
      }
    });

    // Generic file uploads
    document.querySelectorAll('[data-filepond="file"]').forEach((input) => {
      try {
        if (typeof fileUploadService.init === 'function') {
          fileUploadService.init(input, {
            server: {
              url: `${window.location.origin}/GHI/admin/api/upload.php`,
              process: {
                headers: {
                  'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
              },
            },
          });
        }
      } catch (e) {
        console.error('Error initializing file upload:', e);
      }
    });
  } catch (e) {
    console.error('Error in file upload initialization:', e);
  }
}

/**
 * Initialize form handlers
 */
function initializeForms() {
  // Delete confirmation handlers (works with both data-delete-confirm attribute and onclick)
  document.querySelectorAll('[data-delete-confirm], a[href*="-delete.php"]').forEach((button) => {
    const message = button.getAttribute('data-delete-confirm') || 
                   button.getAttribute('onclick')?.match(/confirm\(['"]([^'"]+)['"]/)?.[1] ||
                   'Are you sure you want to delete this item?';
    
    // Remove inline onclick if present
    if (button.hasAttribute('onclick')) {
      button.removeAttribute('onclick');
    }
    
    // Add data attribute if not present
    if (!button.hasAttribute('data-delete-confirm')) {
      button.setAttribute('data-delete-confirm', message);
    }
    
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const confirmMessage = this.getAttribute('data-delete-confirm') || 'Are you sure you want to delete this item?';
      const url = this.getAttribute('href') || this.getAttribute('data-url');

      if (confirm(confirmMessage)) {
        if (url) {
          window.location.href = url;
        }
      }
    });
  });
  
  // Print functionality
  document.querySelectorAll('.print-trigger, button[onclick*="window.print"]').forEach((button) => {
    // Remove inline onclick if present
    if (button.hasAttribute('onclick')) {
      button.removeAttribute('onclick');
    }
    
    // Add class if not present
    if (!button.classList.contains('print-trigger')) {
      button.classList.add('print-trigger');
    }
    
    button.addEventListener('click', function (e) {
      e.preventDefault();
      window.print();
    });
  });

  // Form submissions with validation
  document.querySelectorAll('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const formData = utils.serializeForm(form);
      const schemaName = form.getAttribute('data-schema');

      if (schemaName && formSchemas[schemaName]) {
        const validation = validate(formSchemas[schemaName], formData);
        if (!validation.success) {
          displayFormErrors(form, validation.errors);
          return;
        }
      }

      // Submit form
      form.submit();
    });
  });
}

/**
 * Initialize auto-submit filters
 */
function initializeAutoSubmitFilters() {
  document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
    let submitTimeout;
    
    // Handle input changes with debounce
    form.querySelectorAll('input[type="text"], select').forEach((input) => {
      input.addEventListener('change', () => {
        clearTimeout(submitTimeout);
        submitTimeout = setTimeout(() => {
          form.submit();
        }, 300); // 300ms debounce for text inputs
      });
      
      // For text inputs, also listen to input event for instant feedback
      if (input.type === 'text') {
        input.addEventListener('input', () => {
          clearTimeout(submitTimeout);
          submitTimeout = setTimeout(() => {
            form.submit();
          }, 500); // 500ms debounce for typing
        });
      }
    });
  });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
  const searchInput = document.getElementById('adminSearch');
  if (searchInput) {
    const debouncedSearch = utils.debounce(function (value) {
      // Implement search functionality
      const table = document.querySelector('[data-tabulator]');
      if (table && table.tabulator) {
        table.tabulator.setFilter('title', 'like', value);
      }
    }, 300);

    searchInput.addEventListener('input', function (e) {
      debouncedSearch(e.target.value);
    });
  }
}

/**
 * Display form errors
 */
function displayFormErrors(form, errors) {
  // Clear previous errors
  form.querySelectorAll('.is-invalid').forEach((field) => {
    field.classList.remove('is-invalid');
  });
  form.querySelectorAll('.invalid-feedback').forEach((error) => {
    error.remove();
  });

  // Display errors
  Object.keys(errors).forEach((fieldName) => {
    const field = form.querySelector(`[name="${fieldName}"]`);
    if (field) {
      field.classList.add('is-invalid');
      const errorDiv = document.createElement('div');
      errorDiv.className = 'invalid-feedback';
      errorDiv.textContent = Array.isArray(errors[fieldName]) ? errors[fieldName][0] : errors[fieldName];
      field.parentElement.appendChild(errorDiv);
    }
  });
}

/**
 * Initialize modals for edit/create actions
 */
function initializeModals() {
  // Handle edit/create buttons that should open modals
  document.querySelectorAll('[data-modal-edit], [data-modal-create]').forEach((button) => {
    button.addEventListener('click', async function (e) {
      e.preventDefault();
      const url = this.getAttribute('href') || this.getAttribute('data-url');
      const modalId = this.getAttribute('data-modal-id') || 'editModal';
      const title = this.getAttribute('data-modal-title') || 'Edit Item';
      
      if (!url) return;
      
      // Create or get modal
      let modal = document.getElementById(modalId);
      if (!modal) {
        modal = createModalContainer(modalId);
        document.body.appendChild(modal);
      }
      
      // Update modal title
      const titleEl = modal.querySelector('.modal__title');
      if (titleEl) {
        titleEl.textContent = title;
      }
      
      // Load form content
      const contentEl = modal.querySelector('.modal__content');
      if (contentEl) {
        contentEl.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        try {
          const response = await fetch(url + (url.includes('?') ? '&' : '?') + 'modal=1');
          const html = await response.text();
          
          // Extract form from response
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const form = doc.querySelector('form');
          
          if (form) {
            // Update form action to submit via AJAX
            form.setAttribute('data-ajax-submit', 'true');
            form.setAttribute('data-modal-id', modalId);
            
            contentEl.innerHTML = '';
            contentEl.appendChild(form);
            
            // Re-initialize editors and file uploads in modal
            initializeEditors();
            initializeFileUploads();
            
            // Show modal
            modalService.show(modalId);
          } else {
            contentEl.innerHTML = '<div class="alert alert-danger">Failed to load form. Please try again.</div>';
          }
        } catch (error) {
          console.error('Error loading form:', error);
          contentEl.innerHTML = '<div class="alert alert-danger">Error loading form. Please try again.</div>';
        }
      }
    });
  });
  
  // Handle form submissions in modals
  document.addEventListener('submit', async function (e) {
    const form = e.target;
    if (form.hasAttribute('data-ajax-submit')) {
      e.preventDefault();
      const modalId = form.getAttribute('data-modal-id');
      const formData = new FormData(form);
      
      try {
        const response = await apiService.post(form.action || window.location.href, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        
        if (response.success) {
          notifications.success(response.message || 'Saved successfully!');
          modalService.close(modalId);
          
          // Reload table if exists
          const table = document.querySelector('[data-tabulator]');
          if (table && table.tabulator) {
            table.tabulator.replaceData();
          }
          
          // Reload page after short delay to refresh data
          setTimeout(() => {
            window.location.reload();
          }, 500);
        } else {
          notifications.error(response.message || 'Failed to save');
          if (response.errors) {
            displayFormErrors(form, response.errors);
          }
        }
      } catch (error) {
        console.error('Form submission error:', error);
        notifications.error('An error occurred. Please try again.');
      }
    }
  });
}

/**
 * Initialize modal button handlers
 */
function initializeModalButtons() {
  // Edit links now navigate to pages directly - no modal handling needed
}

/**
 * Create modal container HTML
 */
function createModalContainer(modalId) {
  const modal = document.createElement('div');
  modal.id = modalId;
  modal.className = 'modal';
  modal.setAttribute('aria-hidden', 'true');
  modal.innerHTML = `
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container modal-large" role="dialog" aria-modal="true" aria-labelledby="${modalId}-title">
        <header class="modal__header">
          <h2 class="modal__title" id="${modalId}-title">Edit Item</h2>
          <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content" id="${modalId}-content">
        </main>
        <footer class="modal__footer">
          <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        </footer>
      </div>
    </div>
  `;
  return modal;
}

// Export for use in other admin scripts
export {
  chartService,
  tableService,
  editorService,
  fileUploadService,
  notifications,
  apiService,
  utils,
  modalService,
};
