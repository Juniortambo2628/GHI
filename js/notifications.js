/**
 * Notification Service using Notyf
 * Global Harmony Initiative Website
 */

import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

// Create Notyf instance
const notyf = new Notyf({
  duration: 4000,
  position: {
    x: 'right',
    y: 'top',
  },
  types: [
    {
      type: 'success',
      background: '#4CAF50',
      icon: {
        className: 'notyf__icon--success',
        tagName: 'i',
      },
    },
    {
      type: 'error',
      background: '#f44336',
      icon: {
        className: 'notyf__icon--error',
        tagName: 'i',
      },
    },
    {
      type: 'warning',
      background: '#ff9800',
      icon: {
        className: 'notyf__icon--warning',
        tagName: 'i',
      },
    },
    {
      type: 'info',
      background: '#2196F3',
      icon: {
        className: 'notyf__icon--info',
        tagName: 'i',
      },
    },
  ],
});

/**
 * Show success notification
 */
export function notifySuccess(message, duration = null) {
  return notyf.success(message, duration);
}

/**
 * Show error notification
 */
export function notifyError(message, duration = null) {
  return notyf.error(message, duration);
}

/**
 * Show warning notification
 */
export function notifyWarning(message, duration = null) {
  return notyf.open({
    type: 'warning',
    message: message,
    duration: duration,
  });
}

/**
 * Show info notification
 */
export function notifyInfo(message, duration = null) {
  return notyf.open({
    type: 'info',
    message: message,
    duration: duration,
  });
}

/**
 * Dismiss all notifications
 */
export function dismissAll() {
  notyf.dismissAll();
}

export default {
  success: notifySuccess,
  error: notifyError,
  warning: notifyWarning,
  info: notifyInfo,
  dismissAll,
};

