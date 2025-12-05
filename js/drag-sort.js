/**
 * Drag and Drop Sort Functionality
 * Global Harmony Initiative Website
 * 
 * Handles draggable sorting for both table and grid views
 */

import Sortable from 'sortablejs';
import axios from 'axios';

/**
 * Initialize drag-and-drop for grid views
 */
export function initGridSortable(container, options = {}) {
  const {
    entityType,
    onUpdate,
    sortableOptions = {}
  } = options;

  if (!container || !entityType) {
    console.warn('initGridSortable: container and entityType are required');
    return null;
  }

  const defaultOptions = {
    animation: 150,
    handle: '.drag-handle',
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    ...sortableOptions
  };

  const sortable = Sortable.create(container, {
    ...defaultOptions,
    onEnd: async (evt) => {
      const items = Array.from(container.children);
      const newOrder = items.map((item, index) => {
        const id = item.getAttribute('data-item-id');
        return { id: parseInt(id, 10), order: index + 1 };
      }).filter(item => item.id > 0);

      if (newOrder.length === 0) {
        return;
      }

      // Call custom onUpdate if provided
      if (typeof onUpdate === 'function') {
        const result = await onUpdate(newOrder, entityType);
        if (result === false) {
          // Revert on failure
          sortable.sort(Array.from(container.children).map((_, i) => i));
          return;
        }
      } else {
        // Default: save to API
        await saveOrder(newOrder, entityType);
      }
    }
  });

  return sortable;
}

/**
 * Save order to API
 */
async function saveOrder(items, entityType) {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                  document.querySelector('input[name="_token"]')?.value ||
                  '';

    const response = await axios.post('/GHI/admin/api/update-order.php', {
      entity_type: entityType,
      items: items,
      _token: token
    }, {
      headers: {
        'Content-Type': 'application/json'
      }
    });

    if (response.data.success) {
      // Show success feedback (optional)
      if (window.notyf) {
        window.notyf.success('Order updated successfully');
      }
    } else {
      throw new Error(response.data.message || 'Failed to update order');
    }
  } catch (error) {
    console.error('Failed to save order:', error);
    if (window.notyf) {
      window.notyf.error('Failed to update order. Please try again.');
    }
    throw error;
  }
}

/**
 * Initialize table row reordering (for Tabulator)
 */
export function enableTableRowReorder(table, entityType, onUpdate) {
  if (!table || !entityType) {
    console.warn('enableTableRowReorder: table and entityType are required');
    return;
  }

  let isUpdating = false;

  // Enable row reordering in Tabulator
  table.on('rowMoved', async (row) => {
    // Prevent multiple simultaneous updates
    if (isUpdating) {
      return;
    }

    isUpdating = true;

    try {
      // Get all rows in current order
      const rows = table.getRows();
      const newOrder = rows.map((r, index) => {
        const data = r.getData();
        return { id: parseInt(data.id, 10), order: index + 1 };
      }).filter(item => item.id > 0);

      if (newOrder.length === 0) {
        isUpdating = false;
        return;
      }

      if (typeof onUpdate === 'function') {
        await onUpdate(newOrder, entityType);
      } else {
        await saveOrder(newOrder, entityType);
      }
    } catch (error) {
      // Revert on error - restore original order
      console.error('Failed to save table order:', error);
      table.redraw(true);
    } finally {
      isUpdating = false;
    }
  });
}

/**
 * Add drag handles to grid items
 */
export function addDragHandles(container) {
  const items = container.querySelectorAll('[data-item-id]');
  items.forEach((item) => {
    // Find the admin-grid-card within the item (it might be a direct child or nested)
    const card = item.querySelector('.admin-grid-card') || item;
    
    if (!card.querySelector('.drag-handle')) {
      const handle = document.createElement('div');
      handle.className = 'drag-handle';
      handle.innerHTML = '<i class="bi bi-grip-vertical"></i>';
      handle.setAttribute('aria-label', 'Drag to reorder');
      
      // Position handle on the card
      if (card.classList.contains('admin-grid-card')) {
        // Ensure card has relative positioning
        if (getComputedStyle(card).position === 'static') {
          card.style.position = 'relative';
        }
        card.appendChild(handle);
      } else {
        item.insertBefore(handle, item.firstChild);
      }
    }
  });
}

