/**
 * Error Tracking Service using Sentry
 * Global Harmony Initiative Website
 */

import * as Sentry from '@sentry/browser';

/**
 * Initialize Sentry error tracking
 */
export function initErrorTracking(dsn, options = {}) {
  if (!dsn) {
    console.warn('Sentry DSN not provided. Error tracking disabled.');
    return;
  }

  const defaultOptions = {
    dsn: dsn,
    environment: options.environment || 'production',
    release: options.release || undefined,
    tracesSampleRate: options.tracesSampleRate || 0.1, // 10% of transactions
    beforeSend(event, hint) {
      // Filter out sensitive data
      if (event.request) {
        // Remove sensitive headers
        if (event.request.headers) {
          delete event.request.headers['Authorization'];
          delete event.request.headers['Cookie'];
        }
      }

      // Custom filtering logic
      if (options.beforeSend) {
        return options.beforeSend(event, hint);
      }

      return event;
    },
    ignoreErrors: [
      // Browser extensions
      'top.GLOBALS',
      'originalCreateNotification',
      'canvas.contentDocument',
      'MyApp_RemoveAllHighlights',
      'atomicFindClose',
      'fb_xd_fragment',
      'bmi_SafeAddOnload',
      'EBCallBackMessageReceived',
      'conduitPage',
      // Network errors
      'NetworkError',
      'Failed to fetch',
      'Network request failed',
      // Ignore specific errors
      ...(options.ignoreErrors || []),
    ],
    denyUrls: [
      // Browser extensions
      /extensions\//i,
      /^chrome:\/\//i,
      /^chrome-extension:\/\//i,
      // Facebook plugins
      /connect\.facebook\.net/i,
      /static\.ak\.facebook\.com/i,
      // Other plugins
      /127\.0\.0\.1:4001/i,
      /webappstoolbarba\.texthelp\.com\//i,
      /metrics\.itunes\.apple\.com\.edgesuite\.net\//i,
    ],
  };

  const mergedOptions = { ...defaultOptions, ...options };

  Sentry.init(mergedOptions);

  // Set user context if available
  if (options.user) {
    setUser(options.user);
  }

  return Sentry;
}

/**
 * Set user context
 */
export function setUser(user) {
  Sentry.setUser({
    id: user.id,
    email: user.email,
    username: user.username || user.name,
  });
}

/**
 * Clear user context
 */
export function clearUser() {
  Sentry.setUser(null);
}

/**
 * Capture exception
 */
export function captureException(error, context = {}) {
  Sentry.withScope((scope) => {
    if (context.tags) {
      Object.keys(context.tags).forEach((key) => {
        scope.setTag(key, context.tags[key]);
      });
    }

    if (context.extra) {
      Object.keys(context.extra).forEach((key) => {
        scope.setExtra(key, context.extra[key]);
      });
    }

    if (context.level) {
      scope.setLevel(context.level);
    }

    Sentry.captureException(error);
  });
}

/**
 * Capture message
 */
export function captureMessage(message, level = 'info', context = {}) {
  Sentry.withScope((scope) => {
    if (context.tags) {
      Object.keys(context.tags).forEach((key) => {
        scope.setTag(key, context.tags[key]);
      });
    }

    if (context.extra) {
      Object.keys(context.extra).forEach((key) => {
        scope.setExtra(key, context.extra[key]);
      });
    }

    scope.setLevel(level);
    Sentry.captureMessage(message);
  });
}

/**
 * Add breadcrumb
 */
export function addBreadcrumb(breadcrumb) {
  Sentry.addBreadcrumb(breadcrumb);
}

/**
 * Set context
 */
export function setContext(name, context) {
  Sentry.setContext(name, context);
}

/**
 * Set tag
 */
export function setTag(key, value) {
  Sentry.setTag(key, value);
}

/**
 * Set extra data
 */
export function setExtra(key, value) {
  Sentry.setExtra(key, value);
}

export default {
  init: initErrorTracking,
  setUser: setUser,
  clearUser: clearUser,
  captureException: captureException,
  captureMessage: captureMessage,
  addBreadcrumb: addBreadcrumb,
  setContext: setContext,
  setTag: setTag,
  setExtra: setExtra,
};

