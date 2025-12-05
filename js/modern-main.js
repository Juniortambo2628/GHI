/**
 * Modern Main JavaScript
 * Global Harmony Initiative Website
 * Uses modern ES6+ features and new dependencies
 */

import apiService from './api.js';
import { validate, formSchemas } from './validation.js';
import notifications from './notifications.js';
import utils from './utils.js';
import modalService from './modals.js';
import fileUploadService from './file-upload.js';
import tableService from './tables.js';
import editorService from './editor.js';
import chartService from './charts.js';
import errorTracking from './error-tracking.js';
import animationsOptimized from './animations-optimized.js';
import { initImagePreloader, preloadNextCarouselSlide } from './image-preloader.js';
import { useStore, usePersistentStore } from './store.js';
import modalHandlers from './modal-handlers.js';
import { initLazyLoad, updateLazyLoad } from './lazy-load.js';
import { initScrollAnimations, refreshScrollAnimations } from './scroll-animations.js';

// Initialize error tracking lazily (defer to improve initial load)
// Load after page is interactive
function initErrorTrackingLazy() {
  const sentryDsn = document.querySelector('meta[name="sentry-dsn"]')?.getAttribute('content');
  if (sentryDsn) {
    errorTracking.init(sentryDsn, {
      environment: document.querySelector('meta[name="app-env"]')?.getAttribute('content') || 'production',
      // eslint-disable-next-line no-unused-vars
      beforeSend: (event, hint) => {
        // Add custom context
        errorTracking.setTag('page', window.location.pathname);
        return event;
      },
    });
  }
}

// Defer error tracking until after page is interactive
if (document.readyState === 'complete') {
  // Page already loaded, initialize immediately
  setTimeout(initErrorTrackingLazy, 100);
} else {
  // Wait for page to be interactive
  window.addEventListener('load', () => {
    setTimeout(initErrorTrackingLazy, 500);
  });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initializeApp();
  // Use the optimized animations - they handle all data-animate-on-scroll automatically
  animationsOptimized.init();
  // Initialize intelligent image preloader
  initImagePreloader();
  // Preload next carousel slide when carousel changes
  setupCarouselPreloading();
  // Initialize modal handlers (data-attribute based)
  modalHandlers.initialize();
  // Initialize lazy loading
  initLazyLoad();
  // Initialize AOS scroll animations
  initScrollAnimations();
});

/**
 * Initialize application
 */
function initializeApp() {
  // Initialize form handlers
  initializeForms();

  // Initialize navigation
  initializeNavigation();

  // Initialize other features
  initializeFeatures();
}

/**
 * Initialize form handlers
 */
function initializeForms() {
  // Contact form
  const contactForm = document.querySelector('#contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', handleContactForm);
  }

  // Newsletter form
  const newsletterForm = document.querySelector('#newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', handleNewsletterForm);
  }

  // Login form (if on login page)
  const loginForm = document.querySelector('#login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', handleLoginForm);
  }

  // Get Involved form (volunteer application)
  const getInvolvedForm = document.querySelector('#getInvolvedForm');
  if (getInvolvedForm) {
    getInvolvedForm.addEventListener('submit', handleGetInvolvedForm);
  }
}

/**
 * Handle contact form submission
 */
async function handleContactForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = utils.serializeForm(form);
  const submitButton = form.querySelector('button[type="submit"]');

  // Validate
  const validation = validate(formSchemas.contact, formData);
  if (!validation.success) {
    displayFormErrors(form, validation.errors);
    return;
  }

  // Disable submit button
  submitButton.disabled = true;
  submitButton.textContent = 'Sending...';

  try {
    const response = await apiService.post('/api/contact.php', formData);

    if (response.success && response.data?.success) {
      notifications.success(response.data?.message || 'Thank you! Your message has been sent.');
      form.reset();
      clearFormErrors(form);
    } else {
      const errorMessage = response.data?.message || response.error?.message || response.data?.error?.message || 'Failed to send message. Please try again.';
      notifications.error(errorMessage);
      if (response.data?.errors) {
        displayFormErrors(form, response.data.errors);
      }
    }
  } catch (error) {
    notifications.error('An error occurred. Please try again later.');
    console.error('Contact form error:', error);
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = 'Send Message';
  }
}

/**
 * Handle newsletter form submission
 */
async function handleNewsletterForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = utils.serializeForm(form);
  const emailInput = form.querySelector('input[type="email"]');

  // Validate
  const validation = validate(formSchemas.newsletter, formData);
  if (!validation.success) {
    displayFieldError(emailInput, validation.errors.email?.[0]);
    return;
  }

  try {
    const response = await apiService.post('/api/newsletter.php', formData);

    if (response.success && response.data?.success) {
      notifications.success(response.data?.message || 'Thank you for subscribing!');
      form.reset();
      clearFieldError(emailInput);
    } else {
      const errorMessage = response.data?.message || response.error?.message || response.data?.error?.message || 'Subscription failed. Please try again.';
      notifications.error(errorMessage);
      if (response.data?.errors?.email) {
        displayFieldError(emailInput, response.data.errors.email[0]);
      }
    }
  } catch (error) {
    notifications.error('An error occurred. Please try again later.');
    console.error('Newsletter form error:', error);
  }
}

/**
 * Handle login form submission
 */
async function handleLoginForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = utils.serializeForm(form);

  // Validate
  const validation = validate(formSchemas.login, formData);
  if (!validation.success) {
    displayFormErrors(form, validation.errors);
    return;
  }

  try {
    const response = await apiService.post('/admin/login.php', formData);

    if (response.success) {
      notifications.success('Login successful! Redirecting...');
      setTimeout(() => {
        window.location.href = '/admin/index.php';
      }, 1000);
    } else {
      notifications.error(response.error?.message || 'Invalid email or password.');
    }
  } catch (error) {
    notifications.error('Login failed. Please try again.');
    console.error('Login error:', error);
  }
}

/**
 * Handle get involved form submission
 */
async function handleGetInvolvedForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = utils.serializeForm(form);
  const submitButton = form.querySelector('button[type="submit"]');
  const messageDiv = document.getElementById('formMessage');
  const originalText = submitButton.textContent;

  // Validate required fields
  if (!formData.name || !formData.email || !formData.message) {
    notifications.error('Please fill in all required fields.');
    return;
  }

  // Validate email
  const emailValidation = validate(formSchemas.newsletter, { email: formData.email });
  if (!emailValidation.success) {
    notifications.error('Please enter a valid email address.');
    return;
  }

  submitButton.disabled = true;
  submitButton.textContent = 'Submitting...';
  if (messageDiv) {
    messageDiv.classList.add('d-none');
  }

  try {
    const response = await apiService.post(form.action || '/api/volunteer.php', formData);

    if (response.success && response.data?.success) {
      notifications.success(response.data?.message || 'Thank you! We will contact you soon.');
      form.reset();
      clearFormErrors(form);
      
      // Close modal if Bootstrap modal is available
      const modalElement = form.closest('.modal');
      if (modalElement && window.bootstrap) {
        const modal = window.bootstrap.Modal.getInstance(modalElement);
        if (modal) {
          setTimeout(() => {
            modal.hide();
          }, 2000);
        }
      }
    } else {
      const errorMessage = response.data?.message || response.error?.message || response.data?.error?.message || 'An error occurred. Please try again.';
      notifications.error(errorMessage);
      if (response.data?.errors) {
        displayFormErrors(form, response.data.errors);
      }
      if (messageDiv) {
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = errorMessage;
        messageDiv.classList.remove('d-none');
      }
    }
  } catch (error) {
    notifications.error('An error occurred. Please try again.');
    console.error('Get involved form error:', error);
    if (messageDiv) {
      messageDiv.className = 'alert alert-danger';
      messageDiv.textContent = 'An error occurred. Please try again.';
      messageDiv.classList.remove('d-none');
    }
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = originalText;
  }
}

/**
 * Display form errors
 */
function displayFormErrors(form, errors) {
  // Clear previous errors
  clearFormErrors(form);

  // Display errors
  Object.keys(errors).forEach((fieldName) => {
    const field = form.querySelector(`[name="${fieldName}"]`);
    if (field) {
      displayFieldError(field, errors[fieldName][0]);
    }
  });
}

/**
 * Display field error
 */
function displayFieldError(field, message) {
  field.classList.add('is-invalid');

  // Remove existing error message
  const existingError = field.parentElement.querySelector('.invalid-feedback');
  if (existingError) {
    existingError.remove();
  }

  // Add error message
  const errorDiv = document.createElement('div');
  errorDiv.className = 'invalid-feedback';
  errorDiv.textContent = message;
  field.parentElement.appendChild(errorDiv);
}

/**
 * Clear form errors
 */
function clearFormErrors(form) {
  form.querySelectorAll('.is-invalid').forEach((field) => {
    field.classList.remove('is-invalid');
  });
  form.querySelectorAll('.invalid-feedback').forEach((error) => {
    error.remove();
  });
}

/**
 * Clear field error
 */
function clearFieldError(field) {
  field.classList.remove('is-invalid');
  const errorDiv = field.parentElement.querySelector('.invalid-feedback');
  if (errorDiv) {
    errorDiv.remove();
  }
}

/**
 * Initialize navigation
 */
function initializeNavigation() {
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href !== '#' && href !== '') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
          });
        }
      }
    });
  });
}

/**
 * Initialize other features
 */
function initializeFeatures() {
  // Back to top button
  const backToTop = document.querySelector('.back-to-top');
  if (backToTop) {
    const toggleVisibility = () => {
      const shouldShow = window.scrollY > 300;
      backToTop.classList.toggle('is-visible', shouldShow);
    };

    const scrollHandler = utils.throttle(toggleVisibility, 100);

    window.addEventListener('scroll', scrollHandler);
    toggleVisibility();

    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth',
      });
    });
  }

  // Format dates
  document.querySelectorAll('[data-date]').forEach((element) => {
    const date = element.getAttribute('data-date');
    const format = element.getAttribute('data-date-format') || 'MMMM D, YYYY';
    element.textContent = utils.date.format(date, format);
  });

  // Relative time
  document.querySelectorAll('[data-relative-time]').forEach((element) => {
    const date = element.getAttribute('data-relative-time');
    element.textContent = utils.date.fromNow(date);
  });

  // Initialize file uploads
  initializeFileUploads();

  // Initialize rich text editors
  initializeEditors();

  // Initialize data tables
  initializeTables();

  // Initialize charts
  initializeCharts();
}

/**
 * Initialize GSAP animations
 * Note: This function is now deprecated and handled by animations-optimized.js
 * which provides better performance and smoother animations with proper ScrollTrigger support
 */
function initializeAnimations() {
  // This is now handled by animations-optimized.js
  // which is called in the DOMContentLoaded event listener above
  console.log('Animations are handled by animations-optimized.js');
}

/**
 * Initialize file upload components
 */
function initializeFileUploads() {
  // Image uploads
  document.querySelectorAll('[data-filepond="image"]').forEach((input) => {
    fileUploadService.initImage(input, {
      server: {
        url: '/api/upload/image',
      },
    });
  });

  // Document uploads
  document.querySelectorAll('[data-filepond="document"]').forEach((input) => {
    fileUploadService.initDocument(input, {
      server: {
        url: '/api/upload/document',
      },
    });
  });

  // Generic file uploads
  document.querySelectorAll('[data-filepond="file"]').forEach((input) => {
    fileUploadService.init(input, {
      server: {
        url: '/api/upload',
      },
    });
  });
}

/**
 * Initialize rich text editors
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
 * Initialize data tables
 */
function initializeTables() {
  document.querySelectorAll('[data-tabulator]').forEach((container) => {
    const ajaxUrl = container.getAttribute('data-ajax-url');
    const columnsJson = container.getAttribute('data-columns');

    if (ajaxUrl && columnsJson) {
      try {
        const columns = JSON.parse(columnsJson);
        tableService.initFromAjax(container, ajaxUrl, columns);
      } catch (e) {
        console.error('Error parsing table columns:', e);
      }
    }
  });
}

/**
 * Setup carousel preloading
 */
function setupCarouselPreloading() {
  const carousel = document.getElementById('carouselId');
  if (carousel) {
    carousel.addEventListener('slid.bs.carousel', () => {
      preloadNextCarouselSlide('carouselId');
    });
    // Preload next slide immediately
    preloadNextCarouselSlide('carouselId');
  }
}

/**
 * Initialize charts
 */
function initializeCharts() {
  document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const chartType = canvas.getAttribute('data-chart');
    const dataJson = canvas.getAttribute('data-chart-data');
    const optionsJson = canvas.getAttribute('data-chart-options');

    if (!dataJson) return;

    try {
      const data = JSON.parse(dataJson);
      const options = optionsJson ? JSON.parse(optionsJson) : {};

      switch (chartType) {
        case 'line':
          chartService.line(canvas, data, options);
          break;
        case 'bar':
          chartService.bar(canvas, data, options);
          break;
        case 'pie':
          chartService.pie(canvas, data, options);
          break;
        case 'doughnut':
          chartService.doughnut(canvas, data, options);
          break;
        case 'area':
          chartService.area(canvas, data, options);
          break;
        default:
          console.warn('Unknown chart type:', chartType);
      }
    } catch (e) {
      console.error('Error initializing chart:', e);
    }
  });
}

// Export for use in other modules
export {
  apiService,
  notifications,
  utils,
  modalService,
  fileUploadService,
  tableService,
  editorService,
  chartService,
  errorTracking,
  animations,
  useStore,
  usePersistentStore,
  initLazyLoad,
  updateLazyLoad,
  initScrollAnimations,
  refreshScrollAnimations,
};

