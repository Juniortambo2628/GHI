/**
 * Global Action Menu Manager
 * Handles three-dot menus and contextual right-click menus for admin tables/grids.
 */

class ActionMenuManager {
  constructor() {
    this.menu = null;
    this.origin = null;
    this.boundDocumentClick = this.handleDocumentClick.bind(this);
    this.boundContextClick = this.handleContextClick.bind(this);
    this.boundKeyDown = this.handleKeyDown.bind(this);
    document.addEventListener('click', this.boundDocumentClick);
    document.addEventListener('contextmenu', this.boundContextClick);
    document.addEventListener('keydown', this.boundKeyDown);
    window.addEventListener('resize', () => this.close());
    window.addEventListener('scroll', () => this.close(), true);
  }

  handleDocumentClick(event) {
    const trigger = event.target.closest('[data-action-menu]');

    if (trigger) {
      event.preventDefault();
      event.stopPropagation();
      const actions = this.parseActions(trigger.dataset.actionMenu);
      if (!actions.length) return;
      const rect = trigger.getBoundingClientRect();
      this.origin = trigger;
      this.open(actions, {
        x: rect.right + window.scrollX,
        y: rect.bottom + window.scrollY,
      });
      return;
    }

    if (this.menu && !this.menu.contains(event.target)) {
      this.close();
    }
  }

  handleContextClick(event) {
    const target = event.target.closest('[data-action-menu]');
    if (!target) {
      return;
    }

    event.preventDefault();
    const actions = this.parseActions(target.dataset.actionMenu);
    if (!actions.length) return;
    this.origin = target;
    this.open(actions, {
      x: event.clientX + window.scrollX,
      y: event.clientY + window.scrollY,
    });
  }

  handleKeyDown(event) {
    if (event.key === 'Escape') {
      this.close();
    }
  }

  parseActions(actionsJson) {
    if (!actionsJson) {
      return [];
    }

    try {
      const result = JSON.parse(actionsJson);
      return Array.isArray(result) ? result : [];
    } catch (error) {
      console.error('Failed to parse action menu data', error);
      return [];
    }
  }

  open(actions, position) {
    this.close();

    this.menu = document.createElement('div');
    this.menu.className = 'action-menu-dropdown shadow';

    actions.forEach((action) => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'action-menu-item';
      if (action.danger) {
        item.classList.add('is-danger');
      }
      item.innerHTML = `
        <i class="bi ${action.icon || 'bi-arrow-right-short'} me-2"></i>
        <span>${action.label || 'Action'}</span>
      `;
      item.addEventListener('click', () => {
        if (action.confirm) {
          const confirmed = window.confirm(action.confirm);
          if (!confirmed) {
            return;
          }
        }
        if (action.url) {
          window.location.href = action.url;
        } else if (typeof action.onClick === 'function') {
          action.onClick();
        }
        this.close();
      });

      this.menu.appendChild(item);
    });

    document.body.appendChild(this.menu);

    requestAnimationFrame(() => {
      const menuRect = this.menu.getBoundingClientRect();
      let left = position.x;
      let top = position.y;

      if (left + menuRect.width > window.innerWidth + window.scrollX) {
        left = window.innerWidth + window.scrollX - menuRect.width - 8;
      }
      if (top + menuRect.height > window.innerHeight + window.scrollY) {
        top = window.innerHeight + window.scrollY - menuRect.height - 8;
      }

      this.menu.style.left = `${Math.max(left, 8)}px`;
      this.menu.style.top = `${Math.max(top, 8)}px`;
    });
  }

  close() {
    if (this.menu) {
      this.menu.remove();
      this.menu = null;
      this.origin = null;
    }
  }
}

const actionMenu = new ActionMenuManager();

export default actionMenu;

