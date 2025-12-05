/**
 * Modal Service using MicroModal
 * Global Harmony Initiative Website
 */

import MicroModal from 'micromodal';

// Initialize MicroModal
MicroModal.init({
  onShow: (modal) => {
    // Optional: Add any custom behavior when modal opens
    if (window.log_message) {
      console.log('Modal opened:', modal.id);
    }
  },
  onClose: (modal) => {
    // Optional: Add any custom behavior when modal closes
    if (window.log_message) {
      console.log('Modal closed:', modal.id);
    }
  },
  openTrigger: 'data-micromodal-trigger',
  closeTrigger: 'data-micromodal-close',
  openClass: 'is-open',
  disableScroll: true,
  disableFocus: false,
  awaitOpenAnimation: false,
  awaitCloseAnimation: false,
});

/**
 * Show modal by ID
 */
export function showModal(modalId) {
  MicroModal.show(modalId);
}

/**
 * Close modal by ID
 */
export function closeModal(modalId) {
  MicroModal.close(modalId);
}

/**
 * Close currently open modal
 */
export function closeCurrentModal() {
  const openModal = document.querySelector('.modal.is-open');
  if (openModal) {
    MicroModal.close(openModal.id);
  }
}

/**
 * Update modal content
 */
export function updateModalContent(modalId, title, body, footer = null) {
  const modal = document.getElementById(modalId);
  if (!modal) return false;

  const titleEl = modal.querySelector('.modal-title, [data-micromodal-title]');
  const bodyEl = modal.querySelector('.modal-content, .modal-body');
  const footerEl = modal.querySelector('.modal-footer');

  if (titleEl && title) {
    titleEl.textContent = title;
  }

  if (bodyEl && body) {
    if (typeof body === 'string') {
      bodyEl.innerHTML = body;
    } else if (body instanceof HTMLElement) {
      bodyEl.innerHTML = '';
      bodyEl.appendChild(body);
    }
  }

  if (footerEl && footer) {
    if (typeof footer === 'string') {
      footerEl.innerHTML = footer;
    } else if (footer instanceof HTMLElement) {
      footerEl.innerHTML = '';
      footerEl.appendChild(footer);
    }
  }

  return true;
}

/**
 * Open Bootstrap modal with dynamic data
 * Used for listing detail modals
 */
export function openModal(modalId, data) {
  const modalElement = document.getElementById(modalId);
  if (!modalElement) {
    console.error(`Modal with ID "${modalId}" not found`);
    return;
  }

  const modal = new bootstrap.Modal(modalElement);
  
  // Update modal header with image and title
  const modalHeader = modalElement.querySelector('.modal-header');
  const modalTitle = modalElement.querySelector('.modal-title');
  
  if (data.title && modalTitle) {
    modalTitle.textContent = data.title;
  }
  
  // Set background image if available
  if (modalHeader && data.image) {
    modalHeader.style.backgroundImage = `url('${data.image}')`;
  } else if (modalHeader && data.thumbnail) {
    modalHeader.style.backgroundImage = `url('${data.thumbnail}')`;
  } else if (modalHeader) {
    modalHeader.style.backgroundImage = 'none';
  }
  
  const modalBody = modalElement.querySelector('.modal-body');
  if (modalBody) {
    let html = '';
    
    if (data.subtitle) {
      html += `<p class="text-muted mb-3">${data.subtitle}</p>`;
    }
    
    if (data.description) {
      html += `<div class="mb-3">${data.description}</div>`;
    }
    
    if (data.meta) {
      html += '<div class="row mb-3">';
      for (const [key, value] of Object.entries(data.meta)) {
        const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        html += `<div class="col-md-6 mb-2">
          <strong>${formattedKey}:</strong>
          <span>${value}</span>
        </div>`;
      }
      html += '</div>';
    }
    
    if (data.tags) {
      html += '<div class="mb-3">';
      data.tags.forEach(tag => {
        html += `<span class="badge bg-primary me-1">${tag}</span>`;
      });
      html += '</div>';
    }
    
    modalBody.innerHTML = html;
  }
  
  // Update footer action button
  const footer = modalElement.querySelector('.modal-footer');
  if (footer && data.action_url) {
    const actionBtn = footer.querySelector('a.btn-primary');
    if (actionBtn) {
      actionBtn.href = data.action_url;
      actionBtn.textContent = data.action_text || 'Learn More';
    }
  }
  
  modal.show();
}

// Make openModal available globally for backward compatibility
if (typeof window !== 'undefined') {
  window.openModal = openModal;
}

export default {
  show: showModal,
  close: closeModal,
  closeCurrent: closeCurrentModal,
  updateContent: updateModalContent,
  openModal: openModal,
};

