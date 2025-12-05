/**
 * Data Tables Service using Tabulator
 * Global Harmony Initiative Website
 */

import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator_bootstrap5.min.css';
import { DateTime } from 'luxon';
import actionMenu from './action-menu.js';
import { enableTableRowReorder } from './drag-sort.js';

// Make luxon available globally for Tabulator date formatters
if (typeof window !== 'undefined') {
  window.luxon = { DateTime };
}

/**
 * Initialize Tabulator table
 */
const serializeActionsForAttr = (actions) => {
  try {
    return JSON.stringify(actions || []).replace(/"/g, '&quot;');
  } catch (error) {
    console.error('Failed to serialize action menu', error);
    return '[]';
  }
};

const actionMenuFormatter = (cell) => {
  const data = cell.getData();
  const actions = data.action_menu || [];
  if (!actions.length) {
    return '';
  }

  const actionAttr = serializeActionsForAttr(actions);
  return `
    <button type="button"
            class="btn btn-link p-0 text-muted action-menu-trigger"
            data-action-menu="${actionAttr}"
            aria-label="Open actions">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
  `;
};

export function initTable(container, options = {}) {
  const rowAction = container.getAttribute('data-row-action') || 'auto';
  const modalEntity = container.getAttribute('data-modal-entity') || null;
  const defaultOptions = {
    theme: 'bootstrap5',
    autoColumns: false,
    rowHeader: false,
    pagination: true,
    paginationSize: 10,
    paginationSizeSelector: [10, 25, 50, 100],
    paginationCounter: 'rows',
    layout: 'fitColumns',
    responsiveLayout: 'hide',
    placeholder: 'No Data Available',
    movableColumns: true,
    resizableColumns: true,
    tooltips: true,
    addRowPos: 'top',
    history: true,
    paginationButtonCount: 5,
    ajaxURL: options.ajaxURL || null,
    ajaxConfig: 'GET',
    ajaxContentType: 'json',
    ajaxRequesting: (url, params) => {
      // Show loading indicator
      if (options.onLoading) {
        options.onLoading();
      }
    },
    ajaxResponse: (url, params, response) => {
      // Handle response format
      if (options.onLoaded) {
        options.onLoaded(response);
      }
      
      // If response is an object with data property, return that
      if (response && response.data) {
        return response.data;
      }
      
      return response;
    },
    ajaxError: (error) => {
      console.error('Table AJAX error:', error);
      if (options.onError) {
        options.onError(error);
      }
    },
  };

  const mergedOptions = {
    ...defaultOptions,
    ...options,
  };

  if (Array.isArray(mergedOptions.columns)) {
    mergedOptions.columns = mergedOptions.columns.map((column) => {
      if (!column) {
        return column;
      }

      const col = { ...column };

      const isLegacyActionsField = col.field === 'actions';
      const isActionField = isLegacyActionsField || col.field === 'action_menu';

      if (isLegacyActionsField) {
        col.field = 'action_menu';
      }

      if (isActionField) {
        col.headerSort = false;
        col.hozAlign = col.hozAlign || 'center';
        col.width = col.width || 70;
        col.formatter = actionMenuFormatter;
      } else if (typeof col.formatter === 'string' && col.formatter === 'actionMenu') {
        col.formatter = actionMenuFormatter;
      }

      return col;
    });
  }

  // Check if row reordering is enabled
  const enableRowReorder = container.getAttribute('data-sortable') === 'true';
  const entityType = container.getAttribute('data-entity-type');
  
  if (enableRowReorder) {
    // Add drag handle column if not already present
    const hasDragHandle = mergedOptions.columns?.some(col => col.field === '_drag_handle');
    if (!hasDragHandle && Array.isArray(mergedOptions.columns)) {
      mergedOptions.columns.unshift({
        title: '',
        field: '_drag_handle',
        width: 50,
        headerSort: false,
        formatter: () => '<i class="bi bi-grip-vertical text-secondary"></i>',
        frozen: true
      });
    }
    
    // Enable row reordering
    mergedOptions.movableRows = true;
    mergedOptions.movableRowsConnectedTables = false;
  }

  const table = new Tabulator(container, mergedOptions);
  
  // Initialize row reordering if enabled
  if (enableRowReorder && entityType) {
    enableTableRowReorder(table, entityType);
  }
  
  table.on('rowClick', function (e, row) {
    if (e.target.closest('[data-action-menu]') || e.target.closest('.action-menu-dropdown')) {
      return;
    }

    const data = row.getData();

    if (rowAction === 'none') {
      return;
    }

    if (rowAction === 'modal' && modalEntity && window.modalCRUD) {
      if (data?.id) {
        window.modalCRUD.loadForm(modalEntity, data.id);
      }
      return;
    }

    if (rowAction === 'view' && data?.view_url) {
      window.location.href = data.view_url;
      return;
    }

    if (data?.edit_url) {
      window.location.href = data.edit_url;
      return;
    }

    if (data?.view_url) {
      window.location.href = data.view_url;
    }
  });
  
  // Add right-click context menu
  table.on('rowContext', function(e, row) {
    e.preventDefault();
    const data = row.getData();
    if (data.action_menu && data.action_menu.length) {
      actionMenu.open(data.action_menu, {
        x: e.clientX + window.scrollX,
        y: e.clientY + window.scrollY,
      });
    }
  });
  
  return table;
}

/**
 * Initialize table from AJAX endpoint
 */
export function initTableFromAjax(container, url, columns, options = {}) {
  return initTable(container, {
    ajaxURL: url,
    ajaxConfig: options.method || 'GET',
    columns: columns,
    ...options,
  });
}

/**
 * Initialize table from array data
 */
export function initTableFromData(container, data, columns, options = {}) {
  return initTable(container, {
    data: data,
    columns: columns,
    ...options,
  });
}

/**
 * Export table data
 */
export function exportTable(table, format = 'csv', filename = 'export') {
  if (!table) return;

  switch (format.toLowerCase()) {
    case 'csv':
      table.download('csv', `${filename}.csv`);
      break;
    case 'json':
      table.download('json', `${filename}.json`);
      break;
    case 'xlsx':
      table.download('xlsx', `${filename}.xlsx`);
      break;
    case 'pdf':
      table.download('pdf', `${filename}.pdf`);
      break;
    default:
      table.download('csv', `${filename}.csv`);
  }
}

/**
 * Get selected rows
 */
export function getSelectedRows(table) {
  if (!table) return [];
  return table.getSelectedRows().map(row => row.getData());
}

/**
 * Clear table selection
 */
export function clearSelection(table) {
  if (table) {
    table.deselectRow();
  }
}

export default {
  init: initTable,
  initFromAjax: initTableFromAjax,
  initFromData: initTableFromData,
  initTable: initTable,
  initTableFromAjax: initTableFromAjax,
  initTableFromData: initTableFromData,
  export: exportTable,
  getSelectedRows: getSelectedRows,
  clearSelection: clearSelection,
};

