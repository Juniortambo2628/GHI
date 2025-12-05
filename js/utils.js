/**
 * Utility Functions
 * Global Harmony Initiative Website
 */

import dayjs from 'dayjs';
import serialize from 'form-serialize';
import {
  debounce,
  throttle,
  cloneDeep,
  isEmpty,
  isEqual,
  merge,
  pick,
  omit,
} from 'lodash-es';

// Date utilities using dayjs
export const dateUtils = {
  /**
   * Format date
   */
  format: (date, format = 'YYYY-MM-DD') => {
    return dayjs(date).format(format);
  },

  /**
   * Get relative time (e.g., "2 hours ago")
   */
  fromNow: (date) => {
    return dayjs(date).fromNow();
  },

  /**
   * Add time
   */
  add: (date, amount, unit) => {
    return dayjs(date).add(amount, unit);
  },

  /**
   * Subtract time
   */
  subtract: (date, amount, unit) => {
    return dayjs(date).subtract(amount, unit);
  },

  /**
   * Check if date is valid
   */
  isValid: (date) => {
    return dayjs(date).isValid();
  },

  /**
   * Get difference between dates
   */
  diff: (date1, date2, unit = 'day') => {
    return dayjs(date1).diff(dayjs(date2), unit);
  },
};

// Lodash utilities
export const lodashUtils = {
  debounce,
  throttle,
  cloneDeep,
  isEmpty,
  isEqual,
  merge,
  pick,
  omit,
};

/**
 * Serialize form data using form-serialize library
 */
export function serializeForm(formElement, options = {}) {
  const defaultOptions = {
    hash: true, // Return object instead of string
    empty: false, // Include empty fields
  };

  const mergedOptions = { ...defaultOptions, ...options };
  return serialize(formElement, mergedOptions);
}

/**
 * Serialize form data as URL-encoded string
 */
export function serializeFormString(formElement, options = {}) {
  return serialize(formElement, { hash: false, ...options });
}

/**
 * Debounce function
 */
export function debounceFn(func, wait) {
  return debounce(func, wait);
}

/**
 * Throttle function
 */
export function throttleFn(func, wait) {
  return throttle(func, wait);
}

/**
 * Deep clone object
 */
export function clone(obj) {
  return cloneDeep(obj);
}

/**
 * Check if value is empty
 */
export function isEmptyValue(value) {
  return isEmpty(value);
}

/**
 * Check if two values are equal
 */
export function isEqualValue(value1, value2) {
  return isEqual(value1, value2);
}

export default {
  date: dateUtils,
  lodash: lodashUtils,
  serializeForm,
  debounce: debounceFn,
  throttle: throttleFn,
  clone,
  isEmpty: isEmptyValue,
  isEqual: isEqualValue,
};

