/**
 * Scroll Animations Module
 * Uses AOS (Animate On Scroll) for simple scroll-triggered animations
 */

import AOS from 'aos';
import 'aos/dist/aos.css';

let isInitialized = false;

/**
 * Initialize scroll animations
 */
export const initScrollAnimations = () => {
  if (isInitialized) {
    return;
  }

  AOS.init({
    // Animation duration
    duration: 800,
    // Animation easing
    easing: 'ease-in-out',
    // Animate only once
    once: true,
    // Offset from the original trigger point
    offset: 100,
    // Delay between animations
    delay: 0,
    // Anchor placement
    anchorPlacement: 'top-bottom',
    // Disable on mobile for performance
    disable: function() {
      return window.innerWidth < 768;
    },
    // Debounce delay
    debounceDelay: 50,
    // Throttle delay
    throttleDelay: 99,
  });

  isInitialized = true;

  if (window.log_message) {
    console.log('AOS scroll animations initialized');
  }
};

/**
 * Refresh animations (call after dynamic content is added)
 */
export const refreshScrollAnimations = () => {
  if (isInitialized) {
    AOS.refresh();
  }
};

/**
 * Refresh specific element
 */
export const refreshElement = (element) => {
  if (isInitialized && element) {
    AOS.refreshHard();
  }
};

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initScrollAnimations();
  });
} else {
  initScrollAnimations();
}

export default {
  init: initScrollAnimations,
  refresh: refreshScrollAnimations,
  refreshElement,
};
