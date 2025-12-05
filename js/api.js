/**
 * API Service using Axios
 * Global Harmony Initiative Website
 */

import axios from 'axios';

// Create axios instance with default configuration
const api = axios.create({
  baseURL: window.location.origin,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
      config.headers['X-CSRF-Token'] = csrfToken;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Handle common errors
    if (error.response) {
      // Server responded with error status
      const status = error.response.status;
      
      if (status === 401) {
        // Unauthorized - redirect to login
        window.location.href = '/admin/login.php';
      } else if (status === 403) {
        // Forbidden
        console.error('Access forbidden');
      } else if (status === 404) {
        // Not found
        console.error('Resource not found');
      } else if (status >= 500) {
        // Server error
        console.error('Server error occurred');
      }
    } else if (error.request) {
      // Request made but no response received
      console.error('No response received from server');
    } else {
      // Error setting up request
      console.error('Error setting up request:', error.message);
    }

    return Promise.reject(error);
  }
);

// API methods
export const apiService = {
  /**
   * GET request
   * @param {string} url - API endpoint URL
   * @param {import('axios').AxiosRequestConfig} [config={}] - Axios request configuration
   * @returns {Promise<{success: boolean, data?: any, status: number, headers?: any, error?: any}>} API response
   */
  get: async (url, config = {}) => {
    try {
      const response = await api.get(url, config);
      return {
        success: true,
        data: response.data,
        status: response.status,
        headers: response.headers,
      };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 0,
      };
    }
  },

  /**
   * POST request
   * @param {string} url - API endpoint URL
   * @param {Object|FormData} [data={}] - Request payload
   * @param {import('axios').AxiosRequestConfig} [config={}] - Axios request configuration
   * @returns {Promise<{success: boolean, data?: any, status: number, headers?: any, error?: any}>} API response
   */
  post: async (url, data = {}, config = {}) => {
    try {
      // Handle form data serialization
      if (data instanceof FormData) {
        config.headers = {
          ...config.headers,
          'Content-Type': 'multipart/form-data',
        };
      } else if (typeof data === 'object' && !(data instanceof FormData)) {
        // For URL-encoded forms, convert to URL-encoded string
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken && !data[document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '_token']) {
          const tokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '_token';
          data[tokenName] = csrfToken;
        }
      }
      
      const response = await api.post(url, data, config);
      return {
        success: true,
        data: response.data,
        status: response.status,
        headers: response.headers,
      };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data || { message: error.message },
        status: error.response?.status || 0,
      };
    }
  },

  /**
   * PUT request
   */
  put: async (url, data = {}, config = {}) => {
    try {
      const response = await api.put(url, data, config);
      return {
        success: true,
        data: response.data,
        status: response.status,
        headers: response.headers,
      };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 0,
      };
    }
  },

  /**
   * DELETE request
   */
  delete: async (url, config = {}) => {
    try {
      const response = await api.delete(url, config);
      return {
        success: true,
        data: response.data,
        status: response.status,
        headers: response.headers,
      };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 0,
      };
    }
  },

  /**
   * Upload file with progress tracking
   * @param {string} url - Upload endpoint URL
   * @param {FormData} formData - Form data containing file(s)
   * @param {Function|null} [onProgress=null] - Progress callback function (percentCompleted: number) => void
   * @returns {Promise<{success: boolean, data?: any, status: number, error?: any}>} Upload response
   */
  upload: async (url, formData, onProgress = null) => {
    try {
      const config = {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      };

      if (onProgress) {
        config.onUploadProgress = (progressEvent) => {
          const percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
          onProgress(percentCompleted);
        };
      }

      const response = await api.post(url, formData, config);
      return {
        success: true,
        data: response.data,
        status: response.status,
      };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 0,
      };
    }
  },
};

export default apiService;

